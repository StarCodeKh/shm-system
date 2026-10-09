<?php

namespace App\Http\Controllers;
use App\Models\Dashboard;
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

            // Each block of the dashboard needs the list permission of the module it comes from;
            // blocks the role may not see are neither computed nor sent ("access" tells the page which to show)
            $access = [
                'members' => CommonService::can('list_officer'),
                'income' => CommonService::can('list_income'),
                'expense' => CommonService::can('list_expense'),
                'incentives' => CommonService::can('list_officer_incentive'),
                'payments' => CommonService::can('list_officer_payable') || CommonService::can('list_officer_payment'),
                'requests' => CommonService::can('list_officer_request'),
            ];
            $access['balance'] = $access['income'] && $access['expense']; // balance, quarters and monthly trend
            $khr = fn ($value) => $this->convertToKhmerNumber(CommonService::formatDoubleNum($value));
            $data = ['access' => $access];

            if ($access['members']) {
                $growthData = Dashboard::getOfficerGrowth();
                $data['totalMember'] = Dashboard::getTotalOfficer();
                $data['officerStatisticYears'] = collect($growthData)->pluck('year')->all();
                $data['officerStatisticSeries'] = collect($growthData)->pluck('total_officers')->all();
            }

            if ($access['expense']) {
                $totalExpense = Dashboard::getTotalExpenseKHR($start_date, $end_date);
                $totalExpense->total_in_khr_kh = $khr($totalExpense->total_in_khr);
                $totalExpense->total_in_khr = CommonService::formatDoubleNum($totalExpense->total_in_khr);
                $totalExpense->total_in_usd = CommonService::formatDoubleNum($totalExpense->total_in_usd);
                $data['totalExpense'] = $totalExpense;
            }

            if ($access['income']) {
                $totalIncome = Dashboard::getTotalIncomeKHR($start_date, $end_date);
                $totalIncome->total_in_khr_kh = $khr($totalIncome->total_in_khr);
                $totalIncome->total_in_khr = CommonService::formatDoubleNum($totalIncome->total_in_khr);
                $totalIncome->total_in_usd = CommonService::formatDoubleNum($totalIncome->total_in_usd);
                $data['totalIncome'] = $totalIncome;
                $data['incomeByDepartments'] = [
                    "incomes" => Income::getIncomeByDpts(),
                    "departments" => Income::getDptsByIncome(),
                ];
                $data['monthlyIncomes'] = Income::getMonthlyIncome();
            }

            if ($access['balance']) {
                $totalRemaining = Dashboard::getTotalRemainingBalanceKHR($start_date, $end_date);
                $totalRemaining->remaining_balance_khr_kh = $khr($totalRemaining->remaining_balance_khr);
                $totalRemaining->remaining_balance_khr = CommonService::formatDoubleNum($totalRemaining->remaining_balance_khr);
                $totalRemaining->remaining_balance_usd = CommonService::formatDoubleNum($totalRemaining->remaining_balance_usd);
                $data['totalRemainingBalance'] = $totalRemaining;

                $data['quarterlySummary'] = collect(Dashboard::getQuarterlyIncomeExpense($year, $quarter))->map(function ($item) use ($khr) {
                    $item->total_income_khr_kh = $khr($item->total_income_khr);
                    $item->total_expense_khr_kh = $khr($item->total_expense_khr);
                    $item->remaining_balance_khr_kh = $khr($item->remaining_balance_khr);
                    foreach (['total_income_khr', 'total_income_usd', 'total_expense_khr', 'total_expense_usd', 'remaining_balance_khr', 'remaining_balance_usd'] as $field) {
                        $item->$field = CommonService::formatDoubleNum($item->$field);
                    }
                    return $item;
                });
                $data['monthlyTrend'] = Dashboard::getMonthlyTrend($start_date, $end_date);
                $data['incomeExpComparations'] = [
                    "income" => Income::getMonthlyIncome(),
                    "expense" => Expense::getMonthlyExpense(),
                ];
            }

            if ($access['incentives']) {
                $data['totalIncentive'] = Dashboard::getTotalIncentiveByCategory()->map(function ($item) use ($khr) {
                    $item['total_amount_khr_kh'] = $khr($item['total_amount_khr']);
                    $item['total_amount_khr'] = CommonService::formatDoubleNum($item['total_amount_khr']);
                    $item['total_amount_usd'] = CommonService::formatDoubleNum($item['total_amount_usd']);
                    return $item;
                });
            }

            if ($access['payments']) {
                $data['officerPaymentInfo'] = Dashboard::getOfficerPaymentSummaryFilterByDate($start_date, $end_date);
            }

            if ($access['requests']) {
                $data['requestInfo'] = Dashboard::getRequestInfo();
            }

            return $this->successResponse($data);

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
                __('common.msg_successfully')
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(__('common.validation_failed'), 422, $e->errors());

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
                __('common.msg_successfully')
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
                __('common.msg_successfully')
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
                __('common.msg_successfully')
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
                __('common.msg_successfully')
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
                __('common.msg_successfully')
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
                __('common.msg_successfully')
            );

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
