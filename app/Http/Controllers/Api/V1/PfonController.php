<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Transformers\CustomersTransformer;
use App\Models\Customer_type;
use App\Models\Invoice;
use App\Models\Fastquotation;
use App\Models\Fastpart;

class PfonController extends Controller
{
    public function CustomerTypeList()
    {
		$list=$this->GetChildren();
		return $this->response->array($list->toArray());
    }
	function GetChildren($pid=0){
		$te = Customer_type::where('pid',$pid)->get(['id','name']);
		if(!empty($te))
			foreach($te as $key=>$it)
				$te[$key]['child'] = $this->GetChildren($it['id']);
		return $te;
	}
	function AddInvoices(Request $request){
		$this->validate($request, [
				'payid' => 'required',
		]);
		if($request->submit){
			$this->validate($request, [
				'rise' => 'required',
				'tax_number' => 'required',
				'addressee' => 'required',
				'phone' => 'required',
				'province' => 'required',
				'city' => 'required',
				'region' => 'required',
				'address' => 'required',
			]);
			$inv = new Invoice;
			$inv->tenat_id = $this->GetLoginUser()->tenant_id;
			$inv->payid = $request->payid;//订单编号
			$inv->rise = $request->rise;//单位名称
			$inv->tax_number = $request->tax_number;//税号
			$inv->addressee = $request->addressee;//联系人
			$inv->phone = $request->phone;//电话
			$inv->province = $request->province;//省
			$inv->city = $request->city;//市
			$inv->region = $request->region;//区
			$inv->address = $request->address;//详细地址
			$request->base_account_number && $inv->base_account_number = $request->base_account_number;//银行帐号
			$request->base_account_bank && $inv->base_account_bank = $request->base_account_bank;//开户银行
			$request->company_number && $inv->company_number = $request->company_number;//公司联系电话
			$request->company_register_address && $inv->company_register_address = $request->company_register_address;//公司地址
			$request->feedback && $inv->feedback = $request->feedback;
			$inv->status = 1;
			$inv->save();
			return $this->response->array(['message'=>'ok']);
		}else{
			$ord = Invoice::where('payid',$request->payid)->first();
			if(!empty($ord))
				return $this->response->array($ord->toArray());
			return $this->response->array(['message'=>'此订单没有申请过发票','code'=>1]);
		}
	}
	function GetFastOrder(Request $request){
		$this->validate($request, [
				'key' => 'required',
		]);
		$parts = Fastquotation::where('fast_id',$request->key)->value('parts');
		if(empty($parts))return $this->response->array(['message'=>'无快速报价单信息']);
		$parts = Fastpart::whereIn('id',json_decode($parts,true))->get();
		$parts = $parts->toArray();
		foreach($parts as $key=>$val){
			$parts[$key]['volume_size'] = json_decode($val['volume_size'],true);
		}
		return $this->response->array($parts);
	}
}