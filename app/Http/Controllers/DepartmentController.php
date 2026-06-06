<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        $departments = Department::withCount(['employees', 'projects'])
            ->with('manager.user')
            ->get();
            
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        
        return view('departments.create', compact('employees'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Department::create($request->all());

        return redirect()->route('departments.index')
            ->with('success', 'تم إنشاء القسم بنجاح');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        $department->load([
            'manager.user',
            'employees' => function($query) {
                $query->with('user')->where('status', 'active');
            },
            'projects' => function($query) {
                $query->with(['client'])->latest();
            }
        ]);
        
        $stats = [
            'total_employees' => $department->employees->count(),
            'active_projects' => $department->projects->whereIn('listing_status', ['upcoming', 'active'])->count(),
            'completed_projects' => $department->projects->whereIn('listing_status', ['completed', 'sold_out'])->count(),
            'total_units' => $department->projects->sum('total_units'),
            'available_units' => $department->projects->sum('available_units'),
            'total_budget' => $department->projects->sum('budget'),
            'average_salary' => $department->employees->avg('salary'),
        ];
        
        return view('departments.show', compact('department', 'stats'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        
        return view('departments.edit', compact('department', 'employees'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department->update($request->all());

        return redirect()->route('departments.show', $department)
            ->with('success', 'تم تحديث القسم بنجاح');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        // التحقق من عدم وجود موظفين في القسم
        if ($department->employees()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف القسم لوجود موظفين مرتبطين به');
        }

        // التحقق من عدم وجود مشاريع في القسم
        if ($department->projects()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف القسم لوجود مشاريع مرتبطة به');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'تم حذف القسم بنجاح');
    }

    /**
     * Assign project to department.
     */
    public function assignProject(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $project = Project::find($request->project_id);
        $project->update(['department_id' => $department->id]);

        return redirect()->back()
            ->with('success', 'تم تعيين المشروع للقسم بنجاح');
    }

    /**
     * Assign employee to department.
     */
    public function assignEmployee(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $employee = Employee::find($request->employee_id);
        $employee->update(['department_id' => $department->id]);

        return redirect()->back()
            ->with('success', 'تم تعيين الموظف للقسم بنجاح');
    }

    /**
     * Set department manager.
     */
    public function setManager(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'manager_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $department->update(['manager_id' => $request->manager_id]);

        return redirect()->back()
            ->with('success', 'تم تعيين مدير القسم بنجاح');
    }
}
