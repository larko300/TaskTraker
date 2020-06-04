<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Task;
use Validator;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request['sort_by'] === 'status') {
            $tasks = Task::orderBy('status')->with('user')->get();
        } else {
            $tasks = Task::orderBy('id', 'desc')->with('user')->get();
        }
        return $this->sendResponse($tasks->toArray(), 'Tasks retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|string',
            'description' => 'required|min:3|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $task = Task::create($request->all());
        return $this->sendResponse($task->toArray(), 'Task created successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|string',
            'description' => 'required|min:3|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $task = Task::find($id);
        if (!$task) {
            return $this->sendError('Not found', 'Task with id: ' . $id . ' not found', 400);
        }
        $task->update($request->all());
        return $this->sendResponse($task, 'Task updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function setStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $task = Task::find($id);
        if (!$task) {
            return $this->sendError('Not found', 'Task with id: ' . $id . ' not found', 400);
        }
        $task->update($request->all());
        return $this->sendResponse($task->status, 'Status updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $userId
     * @param $taskId
     * @return JsonResponse
     */
    public function setUserTask($userId, $taskId)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return $this->sendError('Not found', 'Task with id: ' . $taskId . ' not found', 400);
        }
        $user = User::find($userId);
        if (!$user) {
            return $this->sendError('Not found', 'User with id: ' . $userId . ' not found', 400);
        }
        $task->user()->associate($user);
        $task->save();
        return $this->sendResponse($task->toArray(), 'Task\'s user updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->sendError('Not found', 'Task with id: ' . $id . ' not found', 400);
        }
        $task->delete();
        return $this->sendResponse($task->toArray(), 'Task deleted successfully.');
    }
}
