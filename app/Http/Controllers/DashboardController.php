<?php

namespace App\Http\Controllers;
use App\Models\Dashboard;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\User;
use CommonService;
use Exception;


class DashboardController extends BaseController
{
    
    public function getOfficerGrowth()
    {
        try {
            $growthData = Dashboard::getOfficerGrowth();
            return $this->successResponse(
                [
                    'totalMembers' => $growthData
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function totalExpense()
    {
        try {
            $total = Dashboard::getTotalExpenseKHR();
            return $this->successResponse([
                'totalExpense' => $total
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get total expense', '004', [
                'error' => $e->getMessage()
            ]);
        }
    }


    public function totalIncome()
    {
        try {
            $total = Dashboard::getTotalIncomeKHR();
            return $this->successResponse([
                'totalIncome' => $total
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get total income', '004', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRemainingBalance()
    {
        try {
            $total = Dashboard::getTotalRemainingBalanceKHR();
            return $this->successResponse([
                'totalRemainingBalance' => $total
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get total remaining balance', '004', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getTotalIncentiveByCategory()
    {
        try {

            $totalIncentive = Dashboard::getTotalIncentiveByCategory();
            return $this->successResponse(
                [
                    'totalIncentive' => $totalIncentive
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfficerPaymentSummary()
    {
        try {
            $officerPaymentSummary = Dashboard::getOfficerPaymentSummary();
            return $this->successResponse([
                'officerPaymentInfo' => $officerPaymentSummary
              ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

   
    public function quarterlySummary(Request $request)
    {
        try {
            $year = $request->input('year');     // optional
            $quarter = $request->input('quarter'); // optional
            $data = Dashboard::getQuarterlyIncomeExpense($year, $quarter);
            return $this->successResponse([
                'quarterlySummary' => $data
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get quarterly summary', '004', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRequestInfo()
    {
        try {
            $requestInfo = Dashboard::getRequestInfo();
            return $this->successResponse(
                [
                    'requestInfo' => $requestInfo
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getTotalOfficers()
    {
        try {
            $totalOfficer = Dashboard::getTotalOfficer();
            return $this->successResponse(
                [
                    'totalMember' => $totalOfficer
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getDashboardSummary(Request $request)
    {
        try {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $year = $request->input('year');     // optional
            $quarter = $request->input('quarter'); // optional

            $growthData = Dashboard::getOfficerGrowth();
            $totalExpense = Dashboard::getTotalExpenseKHR($start_date, $end_date);
            $totalExpense->total_in_khr_kh =
            $this->convertToKhmerNumber(
                CommonService::formatDoubleNum($totalExpense->total_in_khr)
            );
            $totalExpense->total_in_khr = CommonService::formatDoubleNum($totalExpense->total_in_khr);
            $totalExpense->total_in_usd = CommonService::formatDoubleNum($totalExpense->total_in_usd);

            $totalIncome = Dashboard::getTotalIncomeKHR($start_date, $end_date);
            $totalIncome->total_in_khr_kh =
            $this->convertToKhmerNumber(
                CommonService::formatDoubleNum($totalIncome->total_in_khr)
            );
            $totalIncome->total_in_khr = CommonService::formatDoubleNum($totalIncome->total_in_khr);
            $totalIncome->total_in_usd = CommonService::formatDoubleNum($totalIncome->total_in_usd);


            $totalRemaining = Dashboard::getTotalRemainingBalanceKHR($start_date, $end_date);
            $totalRemaining->remaining_balance_khr_kh =
            $this->convertToKhmerNumber(
                 CommonService::formatDoubleNum($totalRemaining->remaining_balance_khr)
            );
            $totalRemaining->remaining_balance_khr = CommonService::formatDoubleNum($totalRemaining->remaining_balance_khr);
            $totalRemaining->remaining_balance_usd = CommonService::formatDoubleNum($totalRemaining->remaining_balance_usd);

            $totalIncentive = Dashboard::getTotalIncentiveByCategory();
            $totalIncentive = $totalIncentive->map(function ($item) {

                $item['total_amount_khr_kh'] =
                    $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum($item['total_amount_khr'])
                    );
                $item['total_amount_khr'] = CommonService::formatDoubleNum($item['total_amount_khr']);
                $item['total_amount_usd'] = CommonService::formatDoubleNum($item['total_amount_usd']);
                return $item;
            });
            
            
            $officerPaymentSummary = Dashboard::getOfficerPaymentSummaryFilterByDate($start_date,$end_date);

            $TotalQuarterlyIncomeExpense = Dashboard::getQuarterlyIncomeExpense($year, $quarter);
            $TotalQuarterlyIncomeExpense = collect($TotalQuarterlyIncomeExpense)->map(function ($item) {

                $item->total_income_khr_kh =
                    $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum($item->total_income_khr)
                    );

                $item->total_expense_khr_kh =
                    $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum($item->total_expense_khr)
                    );

                $item->remaining_balance_khr_kh =
                    $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum($item->remaining_balance_khr)
                    );

                $item->total_income_khr = CommonService::formatDoubleNum($item->total_income_khr);
                $item->total_income_usd = CommonService::formatDoubleNum($item->total_income_usd);
                $item->total_expense_khr = CommonService::formatDoubleNum($item->total_expense_khr);
                $item->total_expense_usd = CommonService::formatDoubleNum($item->total_expense_usd);
                $item->remaining_balance_khr = CommonService::formatDoubleNum($item->remaining_balance_khr);
                $item->remaining_balance_usd = CommonService::formatDoubleNum($item->remaining_balance_usd);

                return $item;
            });
            $requestInfo = Dashboard::getRequestInfo();
            $totalOfficer = Dashboard::getTotalOfficer();

            $officerStatistics = $growthData;
            $officerStatisticYears = [];
            $officerStatisticSeries = [];
            foreach($officerStatistics as $officerStatistic){
                $officerStatisticYears[] = $officerStatistic["year"];
                $officerStatisticSeries[] = $officerStatistic["total_officers"];
            }

            return $this->successResponse(
                [
                    'totalMember' => $totalOfficer,
                    "officerStatisticYears" => $officerStatisticYears,
                    "officerStatisticSeries" => $officerStatisticSeries,
                    //'totalMemberLatest5Years' => $growthData,
                    'totalExpense' => $totalExpense,
                    'totalIncome' => $totalIncome,
                    'totalRemainingBalance' => $totalRemaining,
                    'totalIncentive' => $totalIncentive,
                    'officerPaymentInfo' => $officerPaymentSummary,
                    'quarterlySummary' => $TotalQuarterlyIncomeExpense,
                    'requestInfo' => $requestInfo,
                    'incomeByDepartments' => [
                        [
                            "department" => "Department1",
                            "income_dollar" => 200,
                            "income_reil" =>  300
                        ],
                        [
                            "department" => "Department1",
                            "income_dollar" => 200,
                            "income_reil" =>  300
                        ],
                        "incomes" => Income::getIncomeByDpts(),
                        "departments" => Income::getDptsByIncome(),
                        // "incomes" => [100, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380],
                        // "departments" => ['អគ្គលេខាធិការដ្ឋានកែទម្រង់', 'មន្ទីរសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ', 'ក្រសួងព្រះបរមរាជវាំង', 'ក្រសួងប្រៃសនីយ', 'ក្រួសួងសុខាភិបាល', 'ក្រសួងអភិវឌ្ឍជនបទ', 'ក្រសួងទូរគមនាគមន៍កម្ពុជា',
                        //     'ក្រសួងកសិកម្ម', 'ក្រសួងរ៉ែនិងថាមពល', 'ក្រសួងពាពិញ្ជកម្ម'
                        // ],
                    ],
                    "monthlyTrend" => Dashboard::getMonthlyTrend($start_date, $end_date),
                    "monthlyIncomes" => Income::getMonthlyIncome(),
                    "incomeExpComparations" => [
                        "income" => Income::getMonthlyIncome(),
                        "expense" => Expense::getMonthlyExpense(),
                    ]
                ]
            );

        }catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function convertToKhmerNumber($number)
    {
        return strtr((string)$number, [
            '0'=>'០','1'=>'១','2'=>'២','3'=>'៣','4'=>'៤',
            '5'=>'៥','6'=>'៦','7'=>'៧','8'=>'៨','9'=>'៩'
        ]);
    }




    public function getUserDetail(Request $request)
    {
        try {
            $userID = $request->input('userID');
            $userDetail = Dashboard::getUserDetail($userID);
            if (!$userDetail) {
                return $this->errorResponse("User not found", "404");
            }
            return $this->successResponse([
                'userDetail' => $userDetail
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getNotificationListing(Request $request)
    {
        try {
            $userID = $request->input('userID');           // required
            $limit  = $request->input('limit', 10);       // default 10 per page
            $page   = $request->input('page', 1);         // default page 1
            // Calculate offset from page number
            $offset = ($page - 1) * $limit;
            // Fetch paginated notifications
            $notifications = Dashboard::getNotificationListByUserId($userID, $limit, $offset);
            // Get total count for pagination
            $totalItems = Dashboard::countNotificationsByUserId($userID);
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'list' => $notifications,
                'page'             => (int)$page,
                'limit'            => (int)$limit,
                'offset'           => (int)$offset,
                'totalItems'       => (int)$totalItems,
                'totalPages'       => (int)$totalPages,
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function getIncomeListing(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);  // Default 10 per page
            $page  = $request->input('page', 1);    // Default page 1
            $offset = ($page - 1) * $limit;
            $list = Dashboard::getIncomeListing($limit, $offset);
            $totalItems = Dashboard::countIncome();
            $totalIncome = Dashboard::getTotalIncomeKHR();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'totalIncome'   => $totalIncome,
                'list'          => $list,
                'page'          => (int)$page,
                'limit'         => (int)$limit,
                'offset'        => (int)$offset,
                'totalItems'    => (int)$totalItems,
                'totalPages'    => (int)$totalPages
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getExpenseListing(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);  // default 10 per page
            $page  = $request->input('page', 1);    // default page 1
            // Calculate offset
            $offset = ($page - 1) * $limit;
            // Fetch paginated expenses
            $list = Dashboard::getExpenseListing($limit, $offset);
            // Get total count
            $totalItems = Dashboard::countExpenses();
            $totalExpense = Dashboard::getTotalExpenseKHR();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'totalExpense' => $totalExpense,
                'list'       => $list,
                'page'       => (int)$page,
                'limit'      => (int)$limit,
                'offset'     => (int)$offset,
                'totalItems' => (int)$totalItems,
                'totalPages' => (int)$totalPages,
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfficerPaymentListing(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);  // default 10
            $page  = $request->input('page', 1);    // default page 1
            // Calculate offset from page number
            $offset = ($page - 1) * $limit;
            // Fetch paginated payment list
            $list = Dashboard::getOfficerPaymentListing($limit, $offset);
            // Get total count for pagination
            $totalItems = Dashboard::countOfficerPayments();
            $officerPaymentInfo = Dashboard::getOfficerPaymentSummary();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'officerPaymentInfo' => $officerPaymentInfo,
                'list'       => $list,
                'page'       => (int)$page,
                'limit'      => (int)$limit,
                'offset'     => (int)$offset,
                'totalItems' => (int)$totalItems,
                'totalPages' => (int)$totalPages,
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfficerIncentiveListing(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);  // default 10
            $page  = $request->input('page', 1);    // default page 1
            // Calculate offset
            $offset = ($page - 1) * $limit;
            // Fetch paginated incentives
            $list = Dashboard::getOfficerIncentiveListing($limit, $offset);
            // Get total count
            $totalItems = Dashboard::countOfficerIncentives();
            $officerIncentiveInfo = Dashboard::getTotalIncentiveByCategory();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'officerIncentiveInfo' => $officerIncentiveInfo,
                'list'       => $list,
                'page'       => (int)$page,
                'limit'      => (int)$limit,
                'offset'     => (int)$offset,
                'totalItems' => (int)$totalItems,
                'totalPages' => (int)$totalPages
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfficerRequestListing(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);   // default 10
            $page  = $request->input('page', 1);     // default page 1
            // Calculate offset
            $offset = ($page - 1) * $limit;
            // Fetch paginated officer requests
            $list = Dashboard::getOfficerRequestListing($limit, $offset);
            // Total count for pagination
            $totalItems = Dashboard::countOfficerRequests();
            $officerRequestInfo = Dashboard::getOfficerRequestSummary();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'officerRequestInfo' => $officerRequestInfo,
                'list'       => $list,
                'page'       => (int)$page,
                'limit'      => (int)$limit,
                'offset'     => (int)$offset,
                'totalItems' => (int)$totalItems,
                'totalPages' => (int)$totalPages
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfficersListing(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10); // default 10
            $page  = (int) $request->input('page', 1);   // default 1
            $offset = ($page - 1) * $limit;
            $list = Dashboard::getOfficersListing($limit, $offset);
            $totalItems = Dashboard::countOfficers();
            $membercounts = Dashboard::countOfficersByGender();
            $totalPages = ceil($totalItems / $limit);

            return $this->successResponse([
                'list'       => $list,
                'page'       => $page,
                'limit'      => $limit,
                'offset'     => $offset,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
                'totalMale'  => $membercounts['totalMale'],
                'totalFemale' => $membercounts['totalFemale'],
                'totalMembers' => $membercounts['total']
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getStatusList(Request $request)
    {
        try {
            // Validate group param
            $request->validate([
                'group' => 'required|string'
            ]);

            $group = $request->group;

            // Call model
            $status = Dashboard::getStatusList($group);

            return $this->successResponse(
                ['status_list' => $status],
                'Status list fetched successfully.'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getTitleList()
    {
        try {
            $titles = Dashboard::getListitles();

            return $this->successResponse(
                ['titles' => $titles],
                'Title list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPaymentMethodList()
    {
        try {
            $data = Dashboard::getAllPaymentMethods();

            return $this->successResponse(
                ['payment_methods' => $data],
                'Payment method list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getAccountList()
    {
        try {
            $data = Dashboard::getAllAccounts();

            return $this->successResponse(
                ['accounts' => $data],
                'Account list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getDutyList()
    {
        try {
            $data = Dashboard::getDutyList();

            return $this->successResponse(
                ['duties' => $data],
                'Duty list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getPositionList()
    {
        try {
            $data = Dashboard::getPositionList();

            return $this->successResponse(
                ['positions' => $data],
                'Position list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getEntityList()
    {
        try {
            $data = Dashboard::getEntityList();

            return $this->successResponse(
                ['entities' => $data],
                'Entity list fetched successfully.'
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}