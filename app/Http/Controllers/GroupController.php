<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUsersRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\GroupUser;
use App\Models\User;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\UserRoleChanged;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Group $group)
    {
        $group->load('currentUserGroup');

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', '=' , 'users.id')
            ->orderBy('users.name')
            ->where('group_id', $group->id)
            ->get();
        $requests = $group->pendingUsers()->orderBy('name')->get();

        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];

        GroupUser::create($groupUserData);

        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', "Group Was Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }

    public function updateImage(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("Permission Denied!", 403);
        }
        $data = $request->validate([
            'cover' => ['nullable', 'image', 'mimes:jpg,png,gif'],
            'thumbnail' => ['nullable', 'image'],
        ]);
        $success = '';

        $cover = $data['cover'] ? $data['cover'] : null;
        $thumbnail = $data['thumbnail'] ? $data['thumbnail'] : null;

        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your Cover Image Was Updated.';
        }

        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);
            $success = "Your Group's Thumbnail Image Was Updated.";
        }


        return back()->with('success', $success);
    }

    public function inviteUsers(InviteUsersRequest $request, Group $group)
    {
        $data = $request->validated();

        $user = $request->user;

        $groupUser = $request->groupUser;

        if ($groupUser) {
            $groupUser->delete();
        }

        $hours = 24;
        $token = Str::random(256);

        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' => Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ]);

        $user->notify(new InvitationInGroup($group, $hours, $token));

        return back()->with('success', "User Was Invited To Join To Group.");
    }

    public function approveInvitation(string $token)
    {
        $groupUser = GroupUser::query()
            ->where('token', $token)
            ->first();

        $errorTitle = '';

        if (!$groupUser) {
            $errorTitle = "The link is not valid";
        } elseif ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = "The link is already used";
        } elseif ($groupUser->token_expire_date < Carbon::now()) {
            $errorTitle = "The link is expired";
        }

        if ($errorTitle) {
            return \inertia('Error', ["title" => $errorTitle]);
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))->with('success', 'You accepted to join to group "' . $groupUser->group->name . '"');
    }

    public function joinUsers(Group $group)
    {
        $user = \request()->user();
        $status = GroupUserStatus::APPROVED->value;
        $successMessage = 'You Have Joined To Group "' . $group->name . '".';

        if (!$group->auto_approval) {
            $status = GroupUserStatus::PENDING->value;

            Notification::send($group->adminUsers, new RequestToJoinGroup($group, $user));
            $successMessage = 'Your Request Has Been Accepted. You Will Be Notified Once You Will Be Approved';
        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $successMessage);
    }

    public function approveRequests(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("Permission Denied!", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'action' => ['required']
        ]);

        $groupUser = GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();

        if ($groupUser){
            $approved = false;
            if ($data['action'] === "approve"){
                $approved = true;
                $groupUser->status = GroupUserStatus::APPROVED->value;
            }else{
                $groupUser->status = GroupUserStatus::REJECTED->value;
            }
            $groupUser->save();

            $user = $groupUser->user;

            $user->notify(new RequestApproved($groupUser->group, $user, $approved));
            return back()->with('success', 'User "'.$groupUser->user->name.'" Was '. ($approved ? "Approved" : "Rejected"));
        }

        return back();
    }

    public function changeRole(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("Permission Denied!", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role' => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $userID = $data['user_id'];
        if ($group->isOwner($userID)){
            return response("You Can't Change Role Of The Owner Of The Group", 403);
        }

        $groupUser = GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser){
            $groupUser->role = $data['role'];
            $groupUser->save();

            $groupUser->user->notify(new UserRoleChanged($group, $data['role']));

            return back()->with('success', 'User "'.$groupUser->user->name.'" Is '.($data['role'] === "admin" ? "Admin" : "User").' From Now');
        }

        return back();
    }
}
