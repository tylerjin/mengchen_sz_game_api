<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\ApiRequest;
use App\Models\Activity;
use App\Models\ActivityReward;
use App\Models\GoodsType;
use App\Models\TaskType;
use App\Services\ApiLog;
use Illuminate\Http\Request;
use Exception;
use App\Models\Tasks;

class ActivitiesController extends Controller
{
    public function showActivities(ApiRequest $request)
    {
        try {
            $activities = Activity::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $activities,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showActivitiesReward(ApiRequest $request)
    {
        try {
            $rewards = ActivityReward::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $rewards,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showTask(ApiRequest $request)
    {
        try {
            $tasks = Tasks::with('typeModel')->get();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $tasks,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showTaskType(ApiRequest $request)
    {
        try {
            $taskTyps = TaskType::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $taskTyps,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showGoodsType(ApiRequest $request)
    {
        try {
            $goodsTypes = GoodsType::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $goodsTypes,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}