<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{

    public function employeeSalaryPay($employe_id) {
        $employee = Employee::findOrFail($employe_id);
        return view('employee.partial.salaryPay',[
            'employee' => $employee
        ]);
    }

    public function paidSalary(Request $request) {
        $request->validate([
            'amount' => 'required',
            'salary_date' => 'required',
            'salary_month' => 'required',
            'salary_year' => 'required',
        ]);

        // dd($request->all());
        if(Salary::where('employee_id',$request->employee_id)->where('salary_month',$request->salary_month)->where('salary_year',$request->salary_year)->exists()) {
            $salary = Salary::where('employee_id',$request->employee_id)->where('salary_month',$request->salary_month)->where('salary_year',$request->salary_year)->get();
            // dd($salary->sum('amount'));
            if($salary->sum('amount') < $request->employee_monthly_salary) {
                $salary = new Salary();
                $salary->employee_id = $request->employee_id;
                $salary->account_id = $request->account_id;
                $salary->pay_by = $request->pay_by;
                $salary->amount = $request->amount;
                $salary->salary_date = $request->salary_date;
                $salary->salary_month = $request->salary_month;
                $salary->salary_year = $request->salary_year;
                if($salary->save()) {
                    return redirect()->route('employee.index')->with('message',"Salary Paid Successfully");
                }
            }
            else{
                return back()->with([
                    'type' => 'error',
                    'message' => "This Month ".$request->employee_name." Salary Taken Allready"
                ]);
            }
        }
        else{
            $salary = new Salary();
            $salary->employee_id = $request->employee_id;
            $salary->account_id = $request->account_id;
            $salary->pay_by = $request->pay_by;
            $salary->amount = $request->amount;
            $salary->salary_date = $request->salary_date;
            $salary->salary_month = $request->salary_month;
            $salary->salary_year = $request->salary_year;
            if($salary->save()) {
                return redirect()->route('employee.index')->with('message',"Salary Paid Successfully");
            }
        }

    
    }

    public function paySalary($employe_id) {
        return view('employee.partial.salaryPay',[
            'employee' => Employee::findOrFail($employe_id),
        ]);
    }  

    public function allSalary() {
        return view('employee.partial.allPaySalary',[
            'months' => Salary::select('salary_month')->groupBy('salary_month')->get()
        ]);
    }

    public function salaryMonth($month) {
        return view('employee.partial.payDetails',[
            'details' => Salary::with('employee')->where('salary_month',$month)->get()
        ]);
    }
    
        // public function allSalary() {
        //   return Salary::select('salary_month')->groupBy('salary_month')->get();
        // }
    
        // public function monthlySalary($month) {
        //   return $details = Salary::with('employee')->where('salary_month',$month)->get();
        //   // return new SalaryResource($details);
        // }
    
}
