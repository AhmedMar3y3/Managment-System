<?php

namespace App\Http\Controllers\Manager;

use App\Models\Chef;
use App\Models\Delivery;
use App\Http\Controllers\Controller;
use App\Notifications\EmployeeAcceptence;
use App\Notifications\EmployeeRejection;

class EmployeeController extends Controller
{

    // new employees requests
    public function Addition()
    {
        $manager = auth('manager')->user();
        $manager_branch = $manager->branch_id;
        $chef = Chef::where('status', 'pending')
            ->with('specialization')
            ->where('branch_id', $manager_branch)
            ->get(['created_at', 'phone', 'first_name', 'last_name', 'image', 'id', 'email', 'specialization_id']);


        $delivery = Delivery::where('status', 'pending')
            ->where('branch_id', $manager_branch)
            ->get(['created_at', 'phone', 'first_name', 'last_name', 'image', 'id', 'email'])
            ->map(function ($item) {
                $item['key'] = 'Delivery Representative';
                return $item;
            });
        return response()->json([
            'chef' => $chef,
            'delivery' => $delivery,
        ], 200);
    }

    public function acceptChef(string $id)
    {
        $chef = Chef::findOrFail($id);
        if ($chef->branch_id === Auth('manager')->user()->branch_id) {
            $chef->update(['status' => 'approved']);
            $chef->notify(new EmployeeAcceptence());
            return response()->json([
                'message' => 'Chef accepted successfully',
            ], 200);
        }
        return response()->json([
            'message' => 'Chef not found or does not belong to your branch',
        ], 404);
    }

    public function rejectChef(string $id)
    {

        $chef = Chef::findOrFail($id);

        if ($chef->branch_id === auth('manager')->user()->branch_id) {
            $chef->update(['status' => 'declined']);

            $chef->notify(new EmployeeRejection());
            return response()->json([
                'message' => 'Chef rejected successfully',
            ], 200);
        }
        return response()->json([
            'message' => 'Chef not found or does not belong to your branch',
        ], 404);
    }

    public function acceptDelivery(string $id)
    {

        $delivery = Delivery::findOrFail($id);
        if ($delivery->branch_id === auth('manager')->user()->branch_id) {

            $delivery->update(['status' => 'approved']);
            $delivery->notify(new EmployeeAcceptence());
            return response()->json([
                'message' => 'Delivery representative accepted successfully',
            ], 200);
        }
        return response()->json([
            'message' => 'Delivery representative not found or does not belong to your branch',
        ], 404);
    }


    public function rejectDelivery(string $id)
    {

        $delivery = Delivery::findOrFail($id);
        if ($delivery->branch_id === auth('manager')->user()->branch_id) {

            $delivery->update(['status' => 'declined']);
            $delivery->notify(new EmployeeRejection());
            return response()->json([
                'message' => 'Delivery representative rejected successfully',
            ], 200);
        }
        return response()->json([
            'message' => 'Delivery representative not found or does not belong to your branch',
        ], 404);
    }
}
