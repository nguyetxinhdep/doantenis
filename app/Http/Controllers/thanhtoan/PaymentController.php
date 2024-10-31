<?php

namespace App\Http\Controllers\thanhtoan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // public function index(Request $req)
    // {
    //     $title = "Quản lý thanh toán";

    //     // Tạo đối tượng query ban đầu
    //     $history = Booking::join('customers', 'bookings.customer_id', '=', 'customers.Customer_id')
    //         ->join('users', 'users.User_id', '=', 'customers.user_id')
    //         ->join('courts', 'bookings.court_id', '=', 'courts.Court_id')
    //         ->join('payments', 'bookings.Booking_id', '=', 'payments.booking_id')
    //         ->select(
    //             'bookings.*',
    //             'users.Name as user_name',
    //             'users.Phone as user_phone',
    //             'courts.name as court_name',
    //             'payments.Debt as Debt',
    //             'payments.Payment_id as Payment_id'
    //         )
    //         ->orderBy('bookings.created_at', 'desc');

    //     // Tìm kiếm theo số điện thoại (loại bỏ số 0 đầu tiên)
    //     if ($req->filled('phone')) {
    //         $phone = ltrim($req->phone, '0');
    //         $history->where('users.Phone', 'like', '%' . $phone . '%');
    //     }

    //     // Tìm kiếm theo tên
    //     if ($req->filled('name')) {
    //         $history->where('users.Name', 'like', '%' . $req->name . '%');
    //     }

    //     // Tìm kiếm theo ngày đặt
    //     if ($req->filled('date')) {
    //         $history->whereDate('bookings.Date_booking', $req->date);
    //     }

    //     // Tìm kiếm theo trạng thái
    //     if ($req->filled('status')) {
    //         $history->where('bookings.Status', $req->status);
    //     }

    //     // Thực hiện phân trang với 10 bản ghi trên mỗi trang
    //     $history = $history->paginate(10);

    //     return view('thanhtoan.index', compact('history', 'title'));
    // }
    public function index(Request $req)
    {
        $title = "Quản lý thanh toán";

        // Tạo đối tượng query ban đầu
        $history = Booking::join('customers', 'bookings.customer_id', '=', 'customers.Customer_id')
            ->join('users', 'users.User_id', '=', 'customers.user_id')
            ->join('courts', 'bookings.court_id', '=', 'courts.Court_id')
            ->join('payments', 'bookings.Booking_id', '=', 'payments.booking_id')
            ->select(
                'bookings.booking_code',
                // 'bookings.Booking_id',
                DB::raw('GROUP_CONCAT(bookings.Date_booking SEPARATOR ", ") as Date_booking'),
                DB::raw('GROUP_CONCAT(bookings.Start_time SEPARATOR ", ") as Start_time'),
                DB::raw('GROUP_CONCAT(bookings.End_time SEPARATOR ", ") as End_time'),
                DB::raw('MAX(bookings.Status) as Status'),
                DB::raw('MAX(users.Name) as user_name'),
                DB::raw('MAX(users.Phone) as user_phone'),
                DB::raw('GROUP_CONCAT(courts.name SEPARATOR ", ") as court_name'),
                DB::raw('SUM(payments.Debt) as Debt'),
                DB::raw('GROUP_CONCAT(payments.Payment_id SEPARATOR ", ") as Payment_id'),
                DB::raw('MAX(bookings.created_at) as created_at') // Thêm trường created_at để sắp xếp
            )
            ->groupBy('bookings.booking_code', 'bookings.Date_booking') // Nhóm theo booking_code
            ->orderBy('created_at', 'desc'); // Sắp xếp theo thời gian tạo mới nhất

        // In ra câu truy vấn SQL
        // dd($history->toSql(), $history->getBindings());
        // Tìm kiếm theo số điện thoại (loại bỏ số 0 đầu tiên)
        if ($req->filled('phone')) {
            $phone = ltrim($req->phone, '0');
            $history->where('users.Phone', 'like', '%' . $phone . '%');
        }

        // Tìm kiếm theo tên
        if ($req->filled('name')) {
            $history->where(
                'users.Name',
                'like',
                '%' . $req->name . '%'
            );
        }

        // Tìm kiếm theo ngày đặt
        if ($req->filled('date')) {
            $history->whereDate(
                'bookings.Date_booking',
                $req->date
            );
        }

        // Tìm kiếm theo trạng thái
        if ($req->filled('status')) {
            $history->where('bookings.Status', $req->status);
        }
        $history->where('bookings.branch_id', session('branch_active')->Branch_id);
        // Thực hiện phân trang với 10 bản ghi trên mỗi trang
        $history = $history->paginate(10);

        return view('thanhtoan.index', compact('history', 'title'));
    }



    public function paymentCourt(Request $req)
    {
        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Lấy thông tin từ request
            $payment_ids = explode(',', $req->input('Payment_id')); // Tách các Payment_id
            // dd($payment_ids);
            $so_tien_thanh_toan = (float) $req->input('paymentAmount');

            foreach ($payment_ids as $payment_id) {
                // Tìm payment record theo từng Payment_id
                $payment = Payment::find($payment_id);

                if (!$payment) {
                    // Trả về thông báo lỗi nếu không tìm thấy payment record nào trong danh sách
                    return redirect()->back()->with('danger', 'Không tìm thấy thông tin thanh toán');
                }

                if ($req->input('PaymentAmount') == "half") {
                    // Tính toán lại số tiền còn nợ và số tiền đã trả
                    $newDebt = $payment->Debt - ($payment->Debt) / 2;
                    $newPaid = $payment->Paid + ($payment->Debt) / 2; // Số tiền đã trả
                } elseif ($req->input('PaymentAmount') == "full") {
                    // Tính toán lại số tiền còn nợ và số tiền đã trả
                    $newDebt = $payment->Debt - $payment->Debt;
                    $newPaid = $payment->Paid + $payment->Debt; // Số tiền đã trả
                }



                // Cập nhật lại số tiền nợ và số tiền đã trả trong bảng payments
                $payment->Debt = $newDebt; // Debt không thể nhỏ hơn 0
                $payment->Paid = $newPaid; // Cập nhật số tiền đã trả

                if ($newDebt <= 0) {
                    $payment->Status = 1; // Đánh dấu hết nợ nếu đã thanh toán đủ
                }
                $payment->Payment_date = now();

                $payment->save(); // Lưu lại từng bản ghi payment

                // tìm booking 
                $booking = Booking::find($payment->booking_id);

                if ($newDebt <= 0) { // Nếu số tiền đã trả hết, cập nhật trạng thái đã trả đủ
                    if ($booking) {
                        $booking->Status = 1; // Cập nhật trạng thái đã thanh toán đủ
                        $booking->save();
                    }
                } else {
                    if ($booking) {
                        $booking->Status = 0; // Cập nhật trạng thái chưa thu đủ
                        $booking->save();
                    }
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
            $payment_ids = explode(',', $req->input('listPayment_id_string')); // Tách các Payment_id
            foreach ($payment_ids as $payment_id) {
                // Tìm payment record theo từng Payment_id
                $payment = Payment::find($payment_id);

                $booking = Booking::find($payment->booking_id);
                // dd($booking);

                if ($booking) {
                    $booking->status = 3; // Cập nhật trạng thái đã hủy
                    $booking->save();
                }
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

    public function searchBookings(Request $req) {}

    // --------------
    public function vnpay_payment(Request $request)
    {
        $code_card = rand(00, 9999); //randum mã đơn hàng để test

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        // $vnp_Returnurl = route('welcome.booking.calendar');
        $vnp_TmnCode = "L7QQHQCG"; //Mã website tại VNPAY 
        $vnp_HashSecret = "O1DDKYJE367YA0UWXSWN8E822Q7TBDH2"; //Chuỗi bí mật

        $vnp_TxnRef = $code_card; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán đơn hàng test";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = ((float) ($request->input('total'))) * 100;
        $kieuthanhtoan = $request->input('redirect');
        if ($kieuthanhtoan == "half") {
            $vnp_Amount = $vnp_Amount / 2; // Chia đôi số tiền
        }
        // if ($request->has('thanhtoan')) {
        //     $kieuthanhtoan = 'full';
        // } elseif ($request->has('datcoc')) {
        //     $kieuthanhtoan = 'half';
        // }
        // $redirectUrl =  route('momo.return');
        $vnp_Returnurl =  route('xulythanhtoanthanhcong', [
            'Payment_id' => $request->Payment_id,
            'pay' =>  $kieuthanhtoan
        ]); //route chuyển tới khi thanh toán thành công-------------
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

    // thanh toán qua atm momo
    public function momo_payment(Request $requests)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create"; // Địa chỉ API MoMo
        $partnerCode = 'MOMOBKUN20180529'; // Mã đối tác của bạn
        $accessKey = 'klm05TvNBzhg7h7j'; // Khóa truy cập
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'; // Khóa bí mật
        $orderInfo = "Thanh toán qua ATM MoMo"; // Thông tin đơn hàng
        $amount = "20000"; // Số tiền mặc định
        $orderId = time() . ""; // Mã đơn hàng dựa trên thời gian

        // Kiểm tra nếu nút 'datcoc' được nhấn
        if ($requests->has('datcoc')) {
            $amount = $amount / 2; // Chia đôi số tiền nếu đặt cọc
        }

        // Các thông tin cần thiết khác
        $extraData = ""; // Dữ liệu bổ sung (nếu cần)
        $requestId = time() . ""; // Mã yêu cầu
        $requestType = "payWithATM"; // Loại yêu cầu thanh toán

        // Xác định kiểu thanh toán
        if ($requests->has('thanhtoan')) {
            $kieuthanhtoan = 'full'; // Thanh toán đầy đủ
        } elseif ($requests->has('datcoc')) {
            $kieuthanhtoan = 'half'; // Thanh toán nửa
        }

        // Đường dẫn chuyển hướng sau khi thanh toán thành công
        $redirectUrl = route('xulythanhtoanthanhcong', [
            'Payment_id' => $requests->Payment_id, // Lấy Payment_id từ request
            'pay' => $kieuthanhtoan // Kiểu thanh toán
        ]);

        $ipnUrl = "http://api.course-selling.id.vn/api/order/payment-notification"; // Đường dẫn thông báo IPN

        // Tạo hash để ký
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey); // Ký HMAC SHA256

        // Dữ liệu gửi đi
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test", // Tên đối tác
            'storeId' => "MomoTestStore", // ID cửa hàng
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

        // Gửi yêu cầu thanh toán
        $result = $this->execPostRequest(
            $endpoint,
            json_encode($data)
        ); // Thực hiện yêu cầu POST
        $jsonResult = json_decode($result, true); // Giải mã JSON

        // Chuyển hướng đến URL thanh toán
        return redirect()->to($jsonResult['payUrl']);
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


    // qr code momo
    public function payMomo(Request $req)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $array = $this->config();

        $partnerCode = $array["partnerCode"];
        $accessKey = $array["accessKey"];
        $secretKey = $array["secretKey"];
        $orderInfo = "Thanh toán qua MoMo";

        // $amount = $req->input('total'); //giá tiền--------------------
        $amount = "10000"; //giá tiền--------------------
        // $amount = 20000; //giá tiền--------------------
        // Kiểm tra nếu nút 'datcoc' được nhấn
        if ($req->has('datcoc')) {
            $amount = $amount / 2; // Chia đôi số tiền
        }

        $orderId = time() . "";
        // $extraData = "merchantName=MoMo Partner";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "captureWallet";
        if ($req->has('thanhtoan')) {
            $kieuthanhtoan = 'full';
        } elseif ($req->has('datcoc')) {
            $kieuthanhtoan = 'half';
        }
        // $redirectUrl =  route('momo.return');
        $redirectUrl =  route('xulythanhtoanthanhcong', [
            'Payment_id' => $req->Payment_id,
            'pay' =>  $kieuthanhtoan
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
}
