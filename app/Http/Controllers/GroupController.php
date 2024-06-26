<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUsersRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\GroupUser;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestToJoinGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Group $group)
    {
        $group->load('currentUserGroup');
        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group)
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
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        //
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
            $path = $cover->store('group-'.$group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your Cover Image Was Updated.';
        }

        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-'.$group->id, 'public');
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

        if (!$groupUser){
            $errorTitle = "The link is not valid";
        }
        elseif ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value){
            $errorTitle = "The link is already used";
        }
        elseif ($groupUser->token_expire_date < Carbon::now()){
            $errorTitle = "The link is expired";
        }

        if ($errorTitle){
            return \inertia('Error', ["title" => $errorTitle]);
        }

        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;

        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));

        return redirect(route('group.profile', $groupUser->group))->with('success', 'You accepted to join to group "'.$groupUser->group->name.'"');
    }

    public function joinUsers(Group $group)
    {
        $user = \request()->user();
        $status = GroupUserStatus::APPROVED->value;
        $successMessage = 'You Have Joined To Group "'.$group->name.'".';

        if (!$group->auto_approval){
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
}
