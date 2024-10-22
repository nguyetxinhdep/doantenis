<?php

namespace App\Http\Controllers\thanhtoan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $req)
    {
        $title = "Quản lý thanh toán";

        $history = Booking::join('customers', 'bookings.customer_id', '=', 'customers.Customer_id')
            ->join('users', 'users.User_id', '=', 'customers.user_id')
            ->join('courts', 'bookings.court_id', '=', 'courts.Court_id')
            ->join('payments', 'bookings.Booking_id', '=', 'payments.booking_id')
            ->select(
                'bookings.*',
                'users.Name as user_name',
                'users.Phone as user_phone',
                'courts.name as court_name',
                'payments.Debt as Debt',
                'payments.Payment_id as Payment_id',
            )
            ->orderBy('bookings.created_at', 'desc')
            ->get();

        return view('thanhtoan.index', compact('history', 'title'));
    }

    public function paymentCourt(Request $req)
    {
        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Lấy thông tin từ request
            $booking_id = $req->input('Booking_id');
            $payment_id = $req->input('Payment_id');
            $so_tien_thanh_toan = (float) $req->input('paymentAmount');

            // Tìm payment record theo payment_id
            $payment = Payment::find($payment_id);

            if (!$payment) {
                // Trả về thông báo lỗi nếu không tìm thấy payment record
                return redirect()->back()->with('danger', 'Không tìm thấy thông tin thanh toán.');
            }

            // Tính toán lại số tiền còn nợ và số tiền đã trả
            $newDebt = $payment->Debt - $so_tien_thanh_toan;
            $newPaid = $payment->Paid + $so_tien_thanh_toan; // Số tiền đã trả

            // Cập nhật lại số tiền nợ và số tiền đã trả trong bảng payments
            $payment->Debt = max(0, $newDebt); // Debt không thể nhỏ hơn 0
            $payment->Paid = $newPaid; // Cập nhật số tiền đã trả
            if ($newDebt <= 0) {
                $payment->Status = 1; //hết nợ
            }
            $payment->save();

            $booking = Booking::find($booking_id);

            if ($newDebt <= 0) { // Nếu số tiền đã trả hết, cập nhật trạng thái đã trả đủ
                if ($booking) {
                    $booking->status = 1; // Cập nhật trạng thái đã thanh toán đủ
                    $booking->save();
                }
            } else {
                if ($booking) {
                    $booking->status = 0; // Cập nhật trạng thái chưa thu đủ
                    $booking->save();
                }
            }

            // Nếu tất cả đều thành công, commit transaction
            DB::commit();

            // Trả về thông báo thành công
            return redirect()->back()->with('success', 'Thanh toán thành công!');
        } catch (\Exception $e) {
            // Nếu có lỗi, rollback tất cả thay đổi
            DB::rollBack();

            return redirect()->back()->with('danger', 'Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.');
        }
    }

    public function cancelCourt(Request $req)
    {
        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Lấy thông tin từ request
            $booking_id = $req->input('Booking_id');

            $booking = Booking::find($booking_id);

            if ($booking) {
                $booking->status = 3; // Cập nhật trạng thái chưa thu đủ
                $booking->save();
            }

            // Nếu tất cả đều thành công, commit transaction
            DB::commit();

            // Trả về thông báo thành công
            return redirect()->back()->with('success', 'Hủy sân thành công!');
        } catch (\Exception $e) {
            // Nếu có lỗi, rollback tất cả thay đổi
            DB::rollBack();

            return redirect()->back()->with('danger', 'Có lỗi xảy ra trong quá trình Hủy sân. Vui lòng thử lại.');
        }
    }

    // --------------
    public function vnpay_payment(Request $request)
    {
        $code_card = rand(00, 9999); //randum mã đơn hàng để test

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/admin";
        $vnp_TmnCode = "L7QQHQCG"; //Mã website tại VNPAY 
        $vnp_HashSecret = "O1DDKYJE367YA0UWXSWN8E822Q7TBDH2"; //Chuỗi bí mật

        $vnp_TxnRef = $code_card; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán đơn hàng test";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = 20000 * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        //Billing
        // $vnp_Bill_Mobile = $_POST['txt_billing_mobile'];
        // $vnp_Bill_Email = $_POST['txt_billing_email'];
        // $fullName = trim($_POST['txt_billing_fullname']);
        // if (isset($fullName) && trim($fullName) != '') {
        //     $name = explode(' ', $fullName);
        //     $vnp_Bill_FirstName = array_shift($name);
        //     $vnp_Bill_LastName = array_pop($name);
        // }
        // $vnp_Bill_Address=$_POST['txt_inv_addr1'];
        // $vnp_Bill_City=$_POST['txt_bill_city'];
        // $vnp_Bill_Country=$_POST['txt_bill_country'];
        // $vnp_Bill_State=$_POST['txt_bill_state'];
        // // Invoice
        // $vnp_Inv_Phone=$_POST['txt_inv_mobile'];
        // $vnp_Inv_Email=$_POST['txt_inv_email'];
        // $vnp_Inv_Customer=$_POST['txt_inv_customer'];
        // $vnp_Inv_Address=$_POST['txt_inv_addr1'];
        // $vnp_Inv_Company=$_POST['txt_inv_company'];
        // $vnp_Inv_Taxcode=$_POST['txt_inv_taxcode'];
        // $vnp_Inv_Type=$_POST['cbo_inv_type'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate"=>$vnp_ExpireDate,
            // "vnp_Bill_Mobile"=>$vnp_Bill_Mobile,
            // "vnp_Bill_Email"=>$vnp_Bill_Email,
            // "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
            // "vnp_Bill_LastName"=>$vnp_Bill_LastName,
            // "vnp_Bill_Address"=>$vnp_Bill_Address,
            // "vnp_Bill_City"=>$vnp_Bill_City,
            // "vnp_Bill_Country"=>$vnp_Bill_Country,
            // "vnp_Inv_Phone"=>$vnp_Inv_Phone,
            // "vnp_Inv_Email"=>$vnp_Inv_Email,
            // "vnp_Inv_Customer"=>$vnp_Inv_Customer,
            // "vnp_Inv_Address"=>$vnp_Inv_Address,
            // "vnp_Inv_Company"=>$vnp_Inv_Company,
            // "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
            // "vnp_Inv_Type"=>$vnp_Inv_Type
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            echo json_encode($returnData);
        }
        // vui lòng tham khảo thêm tại code demo
    }

    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        // dd($result);
        return $result;
    }

    public function momo_payment(Request $requests)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua ATM MoMo";
        $amount = "10000";
        $orderId = time() . "";
        $redirectUrl = "http://127.0.0.1:8000/";
        $ipnUrl = "http://api.course-selling.id.vn/api/order/payment-notification";
        $extraData = "";
        // $partnerCode = $_POST["partnerCode"];
        // $accessKey = $_POST["accessKey"];
        // $serectkey = $_POST["secretKey"];
        // $orderId = $_POST["orderId"]; // Mã đơn hàng
        // $orderInfo = $_POST["orderInfo"];
        // $amount = $_POST["amount"];
        // $ipnUrl = $_POST["ipnUrl"];
        // $redirectUrl = $_POST["redirectUrl"];
        // $extraData = $_POST["extraData"];
        $requestId = time() . "";
        $requestType = "payWithATM";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        // dd($signature);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        // dd(json_encode($data));
        $result = $this->execPostRequest($endpoint, json_encode($data));
        // dd($result);
        $jsonResult = json_decode($result, true);  // decode json
        //Just a example, please check more in there
        return redirect()->to($jsonResult['payUrl']);
        // header('Location: ' . $jsonResult['payUrl']);

    }

    protected function config()
    {
        $config = '
                    {
                        "partnerCode": "MOMOBKUN20180529",
                        "accessKey": "klm05TvNBzhg7h7j",
                        "secretKey": "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa"
                    }
                ';
        return json_decode($config, true);
    }

    public function payMomo(Request $req)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $array = $this->config();

        $partnerCode = $array["partnerCode"];
        $accessKey = $array["accessKey"];
        $secretKey = $array["secretKey"];
        $orderInfo = "Thanh toán qua MoMo";

        $amount = $req->input('total'); //giá tiền--------------------
        // Kiểm tra nếu nút 'datcoc' được nhấn
        if ($req->has('datcoc')) {
            $amount = $amount / 2; // Chia đôi số tiền
        }

        $orderId = time() . "";
        // $extraData = "merchantName=MoMo Partner";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "captureWallet";
        // $redirectUrl =  route('momo.return');
        $redirectUrl =  route('xulythanhtoanthanhcong', [
            'Payment_id' => $req->Payment_id,
            'Booking_id' => $req->Booking_id,
            'pay' =>  $amount
        ]); //route chuyển tới khi thanh toán thành công-------------

        $ipnUrl = "http://api.course-selling.id.vn/api/order/payment-notification";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        // dd($signature);
        $data = array(
            'partnerCode' => $partnerCode,
            // 'partnerName' => "Test",
            // "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        // dd($jsonResult);
        // return $jsonResult;
        return redirect()->to($jsonResult['payUrl']);
    }

    // Phương thức để xử lý kết quả thanh toán từ MoMo
    // public function momoReturn(Request $request)
    // {
    //     http_response_code(200); //200 - Everything will be 200 Oke
    //     $array = $this->config();
    //     try {
    //         $accessKey = $array["accessKey"];
    //         $secretKey = $array["secretKey"];

    //         $partnerCode = $request->input(["partnerCode"]);
    //         $orderId = $request->input(["orderId"]);
    //         $requestId = $request->input(["requestId"]);
    //         $amount = $request->input(["amount"]);
    //         $orderInfo = $request->input(["orderInfo"]);
    //         $orderType = $request->input(["orderType"]);
    //         $transId = $request->input(["transId"]);
    //         $resultCode = $request->input(["resultCode"]);
    //         $message = $request->input(["message"]);
    //         $payType = $request->input(["payType"]);
    //         $responseTime = $request->input(["responseTime"]);
    //         $extraData = $request->input(["extraData"]);
    //         $m2signature = $request->input(["signature"]); //MoMo signature

    //         //Checksum
    //         $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime . "&resultCode=" . $resultCode . "&transId=" . $transId;
    //         $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
    //         // dd($request->input(), ['new' => $partnerSignature]);

    //         if ($m2signature == $partnerSignature) {
    //             // dd('=');
    //             if ($resultCode == '0') {
    //                 // Thanh toán thành công
    //                 $data = [
    //                     'Payment_status' => $message,
    //                     'order_id' => $orderId,
    //                     'amount' => $amount,
    //                     'Payment_method' => $payType
    //                 ];
    //                 order::where('order_id', $orderId)->first()->update(['order_status' => 1, 'checkoutUrl' => 'done']);
    //                 // dd(order::where('order_id', $orderId)->first());
    //                 DB::table('logs')->insert(['log' => json_encode($data)]);
    //             } else {
    //                 // Thanh toán thất bại
    //                 $data = [
    //                     'Payment_status' => $message,
    //                     'order_id' => $orderId,
    //                 ];
    //                 DB::table('logs')->insert(['log' => json_encode($data)]);
    //             }
    //         } else {
    //             // Chữ ký không hợp lệ
    //             // dd('!=');
    //             $data = [
    //                 'danger' => "Giao dịch này có thể bị hack, vui lòng kiểm tra chữ ký của bạn và trả lại chữ ký",
    //                 'order_id' => $orderId,
    //             ];
    //             DB::table('logs')->insert(['log' => json_encode($data)]);
    //         }
    //     } catch (\Exception $e) {
    //         DB::table('logs')->insert(['log' => $e->getMessage()]);
    //     }

    //     $debugger = array();

    //     if ($m2signature == $partnerSignature) {
    //         $debugger['rawData'] = $rawHash;
    //         $debugger['momoSignature'] = $m2signature;
    //         $debugger['partnerSignature'] = $partnerSignature;
    //         $debugger['message'] = "Received payment result success";
    //     } else {
    //         $debugger['rawData'] = $rawHash;
    //         $debugger['momoSignature'] = $m2signature;
    //         $debugger['partnerSignature'] = $partnerSignature;
    //         $debugger['message'] = "ERROR! Fail checksum";
    //     }
    //     return view('success', [
    //         'response' => $request->input(),
    //         'debugger' => $debugger
    //     ]);
    // }
}
