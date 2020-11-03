<?php
namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use SCart\Core\Front\Models\ShopOrder;

class ShippingController extends Controller {

    protected $client;
    protected $sendShippingURL = 'http://localhost:8083/api/placeshippingorder';
    protected $checkShippingStatusURL = 'http://localhost:8081/api/checkshippingstatus';
    protected $changeShippingStatus = 'http://localhost:8082/api/changeshippingstatus';


    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendShipping(Request $request)
    {
        $order_id = $request->get('id');
        $resp = $this->client->request('POST', $this->sendShippingURL, ['json' => array('order_id' => $order_id)]);
        if ($resp->getStatusCode() === 200) {
            $respBody = $resp->getBody();
            $data = @json_decode($respBody->getContents());
            $tracking_number = $data->tracking_number;
            ShopOrder::where('id', $order_id)->update(['tracking_number' => $tracking_number, 'shipping_status' => 2]);
            //Response data to user;
            return response()->json([
                'status' => true,
                'order_id' => $order_id,
                'data' => $data
            ]);
        }
        //Fail
        return response()->json([
            'status' => false,
            'message' => 'Cannot request send to shipping service'
        ]);
    }

    public function changeShippingStatus(Request $request)
    {
        $tracking_number = $request->get('tracking_number');
        $resp = $this->client->request('POST', $this->changeShippingStatus, ['json' => array('tracking_number' => $tracking_number, 'status' => 3)]);
        if ($resp->getStatusCode() === 200) {
            $respBody = $resp->getBody();
            $data = @json_decode($respBody->getContents());
            $message = $data->status;
            return response()->json([
                'status' => true,
                'tracking_number' => $tracking_number,
                'message' => $message,
            ]);
        }
        //Fail
        return response()->json([
            'status' => false,
            'message' => 'Cannot request send to shipping service'
        ]);
    }

    public function checkShippingStatus(Request $request)
    {
        $tracking_number = $request->get('tracking_number');
        $resp = $this->client->request('POST', $this->checkShippingStatusURL, ['json' => array('tracking_number' => $tracking_number)]);
        if ($resp->getStatusCode() === 200) {
            $respBody = $resp->getBody();
            $data = @json_decode($respBody->getContents());
            if ($data->data[0]->shipping_status === 2)
            {
                $message = "Your Order Is Delivering!";
            } else if ($data->data[0]->shipping_status === 3)
            {
                $message = "Your Order Was Delivered!";
            }
            return response()->json([
                'status' => true,
                'tracking_number' => $tracking_number,
                'data' => $data,
                'message' => $message
            ]);
        }
        //Fail
        return response()->json([
            'status' => false,
            'message' => 'Cannot request send to shipping service'
        ]);
    }
}