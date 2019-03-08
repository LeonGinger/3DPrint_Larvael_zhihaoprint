<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UserRole;
use App\Models\Department;
use App\Models\User;
use App\Models\Customer;
use EasyWeChat\Factory;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function GetLoginUser(){
		return \Auth::guard('api')->getUser();
	}
	public function CheckRoleAction($action = '',$role = array()){
		$loguser = \Auth::guard('api')->getUser();
		$role = array_merge($role,array('admin'));
		if($loguser->dep_ids){
			$dep = Department::where('id',$loguser->dep_ids)->value('depcode');
			if(!empty(array_intersect($role,array($dep)))){
				if($loguser->pr_id){
					$urle = UserRole::where('id',$loguser->pr_id)->select('depcode','action')->first();
					if(empty(array_intersect(json_decode($urle->depcode,true),$role)))
						return $this->response->errorUnauthorized('您无此权限');
					if(empty(array_intersect(json_decode($urle->action,true),array($action))))
						return $this->response->errorUnauthorized('您无此权限');
				}
			}
			else
				return $this->response->errorUnauthorized('您无此权限');
		}
	}
	public function SendWechatMessage($touser,$templ,$type,$orderno = '',$url = ''){
		$opid = '';
		switch($touser['utype']){
			case 'system':
				$opid = User::where('id',$touser['uid'])->value('openid');
				break;
			case 'customer':
				$opid = Customer::where('id',$touser['uid'])->value('openid');
				break;
		}
		if(!empty($opid)){
			$sdata = array(
				'touser' => $opid,
				'template_id' => '',
				'url' => $url,
				'data' => '',
			);
			$bdata = array();
			switch($templ){
				case 1:
					$sdata['template_id'] = 's1OfIiOQojWr0I_ScJI-vKqy8eLNqaw9RMaPU-rM4Ow';break;
				case 2:
					$sdata['template_id'] = '0GFIcml6So6hEq1Qc94iw5q3-JQ9DsZ9SmGP4LQNMa0';break;
			}
			switch($templ){
				case 1:
				case 2:
					$bdata = array(
						'first' => '',
						'keyword1' => '',
						'keyword2' => '',
						'remark' => '',
					);
					break;
			}
			switch($type){
				case 1:
					$bdata['first'] = '您有一张新订单';
					$bdata['keyword1'] = date('Y-m-d');
					$bdata['keyword2'] = $orderno;
					$bdata['remark'] = '报价单已经生成，点击查看详细，如有疑问请及时联系订单销售工程师咨询。';
					break;
				case 2:
					$bdata['first'] = '有一张订单客户已确认';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '客户已确认';
					$bdata['remark'] = '请登陆平台继续下一步操作。';
					break;
				case 3:
					$bdata['first'] = '有零件开始生产了';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已开始生产';
					$bdata['remark'] = '请点击查看详细';
					break;
				case 4:
					$bdata['first'] = '订单已经生产完成';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已完成生产';
					$bdata['remark'] = '请点击查看详细';
					break;
				case 5:
					$bdata['first'] = '您的订单已经全部发货，请注意查收';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已全部发货';
					$bdata['remark'] = '点击查看详情';
					break;
				case 6:
					$bdata['first'] = '有一个新的生产任务分配给您';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '准备生产';
					$bdata['remark'] = '请点击并查看《生产任务书》';
					break;
				case 7:
					$bdata['first'] = '客户修改了一个订单，请重新审核';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '等待审核';
					$bdata['remark'] = '点击查看详情';
					break;
				case 8:
					$bdata['first'] = '订单已经发货，请登录平台，查看详细。';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已发货';
					$bdata['remark'] = '点击登录平台-我的订单-查看详细内容。';
					break;
				case 9:
					$bdata['first'] = '您的订单已经分批发货，请注意查收';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已分批发货';
					$bdata['remark'] = '点击查看详情';
					break;
				case 10:
					$bdata['first'] = '客户已确认全部收货，订单完成！';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已完成';
					$bdata['remark'] = '点击登录平台-我的订单-查看详细内容。';
					break;
				case 11:
					$bdata['first'] = '客户已确认部分收货，请尽快安排剩余零件发货。';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '部分收货';
					$bdata['remark'] = '点击登录平台-我的订单-查看详细内容。';
					break;
				case 12:
					$bdata['first'] = '零件已收货，订单完成！请您评价~';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已完成';
					$bdata['remark'] = '该订单的零件已经全部收货，订单已经结束，请您对本次体验做出评价~';
					break;
				case 13:
					$bdata['first'] = '部分零件已收货，将尽快完成剩余零件发货。';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '分批收货';
					$bdata['remark'] = '该订单的零件已分批送货，您已确认收到分批发货零件，剩余零件我们将尽快发货。';
					break;
				case 14:
					$bdata['first'] = '您申请正在取消订单';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '退单';
					$bdata['remark'] = '您的退单申请我们已经收到，请等待相关人员处理。';
					break;
				case 15:
					$bdata['first'] = '客户提交退单申请';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '退单';
					$bdata['remark'] = '客户已提交退单申请，请登录平台处理。';
					break;
				case 16:
					$bdata['first'] = '送货单超过7未确认，系统已自动收货！';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '自动收货';
					$bdata['remark'] = '订单已经完成，点击登录平台-我的订单-查看详细内容。';
					break;
				case 17:
					$bdata['first'] = '送货单超过7未确认，系统已自动收货！';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '自动收货';
					$bdata['remark'] = '订单已部分确认收货，点击登录平台-我的订单-查看详细内容。';
				case 18:
					$bdata['first'] = '送货单超过7未确认，系统已自动收货！';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '已完成';
					$bdata['remark'] = '该订单的零件已经全部收货，订单已经结束，请您对本次体验做出评价~';
					break;
				case 19:
					$bdata['first'] = '送货单超过7未确认，系统已自动收货！';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '分批收货';
					$bdata['remark'] = '该订单的零件已分批送货，您已确认收到分批发货零件，剩余零件我们将尽快发货。';
					break;
				case 20:
					$bdata['first'] = '该订单已经取消';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '取消订单';
					$bdata['remark'] = '客户申请取消订单已经通过审核，成功取消订单，请相关人员留意。';
					break;
				case 21:
					$bdata['first'] = '订单已经取消';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '取消订单';
					$bdata['remark'] = '您申请取消订单已经通过审核，成功取消订单。';
					break;
				case 22:
					$bdata['first'] = '订单零件重做';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '零件重做';
					$bdata['remark'] = '客户申请零件重做已经通过审核，零件已经添加进待加工零件库，请相关人员留意。';
					break;
				case 23:
					$bdata['first'] = '零件重做申请已通过';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '零件重做';
					$bdata['remark'] = '您申请零件重做已经通过审核，我们将尽快从新安排生产。';
					break;
				case 24:
					$bdata['first'] = '零件重做申请';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '零件重做';
					$bdata['remark'] = '客户提交零件重做申请，请您登陆平台审核。';
					break;
				case 25:
					$bdata['first'] = '您申请正在零件重做';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '零件重做';
					$bdata['remark'] = '您的零件重做申请我们已经收到，请等待相关人员处理。';
					break;
				case 26:
					$bdata['first'] = '您的订单已经确认';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '确认订单';
					$bdata['remark'] = '您已经确认了订单，我们将尽快安排生产，您可以在“我的订单”列表查看更多信息。';
					break;
				case 27:
					$bdata['first'] = '有一张新的订单等待审核';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '再次下单';
					$bdata['remark'] = '客户通过再次下单功能，添加了新的订单，请登陆平台审核！';
					break;
				case 28:
					$bdata['first'] = '您添加了一张新的订单，请等待服务商审核。';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '再次下单';
					$bdata['remark'] = '您使用再次下单功能添加订单成功，等待服务商审核后生效！';
					break;
				case 29:
					$bdata['first'] = '客户已对您做出了评价';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '客户评价';
					$bdata['remark'] = '订单已经完成并且客户已经提交了评价，请点击查看评价详细内容。';
					break;
				case 30:
					$bdata['first'] = '您已经解除绑定';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '解除绑定';
					$bdata['remark'] = '您已经成功解除了绑定。';
					break;
				case 31:
					$bdata['first'] = '您的售后申请未通过';
					$bdata['keyword1'] = $orderno;
					$bdata['keyword2'] = '售后申请未通过';
					$bdata['remark'] = '商家拒绝了您的售后申请，请点击查看详细原因。';
					break;
			}
			$sdata['data'] = $bdata;
			$app = Factory::officialAccount(config('wechat'));
			$app->template_message->send($sdata);
		}
	}
}