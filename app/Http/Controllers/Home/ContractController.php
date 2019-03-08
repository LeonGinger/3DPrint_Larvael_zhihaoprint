<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Part;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Linkman;
use App\Models\User;
use App\Models\TenantAd;
use App\Models\PlantFormSetting;
use App\Models\Fastquotation;
use App\Models\Fastpart;
use App\Models\Tenant;
use TCPDF;
use Illuminate\Contracts\Encryption\DecryptException;

class ContractController extends Controller
{
	public function GetContract(Request $request){
		$oid = decrypt($request->Input('val'));
		$qut = Quotation::where('order_id', $oid)->first();
		$order = Order::find($oid);
		if(empty($order))abort(403, '无订单信息');
		$quot = Quotation::where('order_id', $oid)->first();
		if(empty($quot))abort(403, '报价单错误');
		$cus = Customer::where('id', $order->customer_id)->select('name','ticket_info','tenat_id')->first();
		$lks = explode(',', $order->lkmans);
		$lks = Linkman::whereIn('id', $lks)->select('*')->get();
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->get();
		
		$out = $quot->toArray();
		$out['cusname'] = $cus->name;
		$out['tanname'] = Tenant::where('id',$order->tenat_id)->value('name');
		$out['ticket_info'] = unserialize($cus->ticket_info);
		$out['linkmans'] = $lks->toArray();
		$out['postaddr'] = Linkman::find($order->postaddr);
		$out['invoaddr'] = Linkman::find($order->invoaddr);
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts->toArray();
		$out['taxation'] = $order->taxation;
		$out['freight'] = $order->freight;
		
		
		$rnd = date("Ymdhis").rand(1111,9999);
		$path = md5($oid.'2dv8WUCU');
		$rot = 'contract/'.$path.'/';
		if(!file_exists(public_path($rot)))
				mkdir(public_path($rot));
		//if (View::exists('wechat.contract')) {
			//$view = View::make('wechat.contract')->with('data',$out);
			//$html = response($view)->getContent();
			//return $this->response->array($html);
			//PDF::loadHTML($html)->setPaper('a4')->save('eliverynote.pdf');
			//PDF::loadHTML($html)->setPaper('a4')->setWarnings(false)->save($rot);
			//return $this->response->array($html);
		//}
		if($order->contract == null)
			Order::where('id', $oid)->update(['contract' =>  env('APP_URL').'/wechat/order/contract?val='.$request->Input('val')]);
		//Order::where('id', $oid)->update(['contract' =>  env('APP_URL').'/'.$rot]);
		
		 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT,true, 'UTF-8', true);
		        // 设置文档信息
        // set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('TPM3D');
$pdf->SetTitle('TPM3D');
$pdf->SetSubject('TPM3D');
$pdf->SetKeywords('TPM3D, PDF');

// set default header data
$ad = TenantAd::where('tenant_id',$cus->tenat_id)->where('page','合同')->select('loca','imgurl')->get();
$adt = '';$adf = '';
if(count($ad) !=2 )$pfs = PlantFormSetting::first();
foreach($ad as $v){
	if($v['loca'] == 'foot')
		$adf = str_replace_first(env('APP_URL').'/storage/', '../../../../../storage/app/public/', $v['imgurl']);
	if($v['loca'] == 'top')
		$adt = str_replace_first(env('APP_URL').'/storage/', '../../../../../storage/app/public/', $v['imgurl']);
}
if($adt == '')$adt=str_replace_first(env('APP_URL'), '../../../../../public', $pfs->ad_header_url);
if($adf == '')$adf=str_replace_first(env('APP_URL'), '../../../../../public', $pfs->ad_footer_url);

$pdf->SetHeaderData($adt, 40, 'TPM3D', 'www.tpm3d.com', array(0,64,255), array(0,64,128));
$pdf->setFooterData($adf, 30, '', '', array(0,64,0), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('stsongstdlight', '', 12);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = '
<h1 style="text-align:center">报价单(合同)</h1>
<h2 style="text-align:center">供需方信息区域</h2>
<p>报价单号 : '.$out['no'].'<br/>
报价日期 : '.$out['created_at'].'</p>
<p>
<b>购买方</b><br/>
客户名称 : '.$out['cusname'].'
</p>
<table>';
for($i=0;$i<count($out['linkmans']);$i++){
	$lxr = $i==0? '联系人' : '';
	$html .= '<tr><td width="80">'.$lxr.'</td><td>'.$out['linkmans'][$i]['linkman_name'].' <span style="color: rgb(0, 128, 64)">'.$out['linkmans'][$i]['lk_phone'].'</span></td></tr>';
}
$html .= '</table>
<br/><br/>
<table>
	<tr><td width="80">送货地址</td><td width="720">'.$out['postaddr']['linkman_name'].' <span style="color: rgb(0, 128, 64)">'.$out['postaddr']['lk_phone'].'</span></td></tr>
	<tr><td></td><td>'.$out['postaddr']['province'].$out['postaddr']['city'].$out['postaddr']['area'].$out['postaddr']['lk_address'].'</td></tr>
</table>
';
if(!empty($out['ticket_info']['taxno']) && !empty($out['ticket_info']['bank']) && !empty($out['ticket_info']['bankno']) && !empty($out['invoaddr'])){
	$html .= '<br/><br/><table>
		<tr><td width="80">发票信息</td><td width="70">税&nbsp;&nbsp;号</td><td>'.$out['ticket_info']['taxno'].'</td></tr>
		<tr><td></td><td>开户行</td><td>'.$out['ticket_info']['bank'].'</td></tr>
		<tr><td></td><td>账号</td><td>'.$out['ticket_info']['bankno'].'</td></tr>
	</table>
	<p><table>
		<tr><td width="80">发票邮寄地址</td><td width="720">'.$out['invoaddr']['linkman_name'].' <span style="color: rgb(0, 128, 64)">'.$out['invoaddr']['lk_phone'].'</span>
		<br/>'.$out['invoaddr']['province'].$out['invoaddr']['city'].$out['invoaddr']['area'].$out['invoaddr']['lk_address'].'</td></tr>
	</table>
	</p>';
}
$html .= '<p><b>制作方</b><br/>
服务商 : '.$out['tanname'].'<br/>
 销售工程师 : '.$out['saler']['name'].' <span style="color: rgb(0, 128, 64)">'.$out['saler']['phone'].'</span><br/>
 项目工程师 : '.$out['pojer']['name'].' <span style="color: rgb(0, 128, 64)">'.$out['pojer']['phone'].'</span><br/>
 项目经理 : '.$out['manger']['name'].' <span style="color: rgb(0, 128, 64)">'.$out['manger']['phone'].'</span></p>
 <h2 style="text-align:center">零件清单区域</h2>
';

$pdf->writeHTML($html, true, false, true, false, '');
$imgdata = base64_decode(base64_encode(file_get_contents($out['qrcode_url'])));
$pdf->Image('@'.$imgdata, 140, 55, 40, 40, '', false, '', false, 150, '', false, false, 0, false, false, false);

$y = $pdf->getY()+5;
$x = 15;
$pc = count($out['parts']);
$pi = 0;
for($i = 0;$i<3;++$i){
	for($j = 0;$j<2;++$j){
		$pdf->SetXY($x,$y);
		$pdf->SetFont('', '', 12);
		$pdf->Cell(0, 6, $out['parts'][$pi]['name']);
		$pdf->SetXY($x,$y+6);
		$pdf->SetFont('', '', 10);
		$pdf->Cell(0, 5, $out['parts'][$pi]['matname'].'-'.$out['parts'][$pi]['modname'].'-'.$out['parts'][$pi]['surname'].'-'.$out['parts'][$pi]['equname']);
		$pdf->Rect($x, $y+11, 28, 20, 'F', [], array(216,216,216));
		$fitbox = 'C'.' ';
		$fitbox[1] = 'M';
		$imgdata = base64_decode(str_after($out['parts'][$pi]['diagram'], 'data:image/jpeg;base64,'));
		$pdf->Image('@'.$imgdata, $x, $y+11, 28, 20, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
		$pdf->SetXY($x+30,$y+11);
		$pv = json_decode($out['parts'][$pi]['volume_size'],true);
		$pdf->Cell(0, 5, '尺寸:'.$pv['xx'].'*'.$pv['yy'].'*'.$pv['zz'].' 体积:'.$pv['volume']);
		$pdf->SetXY($x+30,$y+16);
		$pdf->Cell(0, 5, '数量: '.$out['parts'][$pi]['product_num'].'件  单价: ￥'.number_format($out['parts'][$pi]['price']*$out['parts'][$pi]['coefficient'], 2));
		$pdf->SetXY($x+30,$y+21);
		$pdf->Cell(0, 5, '合计: ￥'.number_format($out['parts'][$pi]['product_num'] * $out['parts'][$pi]['price']*$out['parts'][$pi]['coefficient'], 2));
		$pdf->SetXY($x+30,$y+26);
		$pdf->Cell(0, 5, '启动:'.date('Y-m-d',strtotime($out['parts'][$pi]['start_date'])).' 交货:'.date('Y-m-d',strtotime($out['parts'][$pi]['due_date'])));
		$x = $x + 92;
		$pi++;
		if($pi>=$pc)break;
	}
	$y = $y+35;
	if($y > 248) {$y =  20;$pdf->AddPage();}
	$x=15;
	if($pi>=$pc)break;
	//if($i == 1){$y=$pdf->getY();}
}
// write the first column
$pdf->SetXY($x,$y);
$pdf->Ln(1);
$pdf->SetFont('', '', 12);
$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [216,216,216]));
$html = '
<p><hr /></p>
<p>税费：￥'.$out['taxation'].'</p>
<p>运费：￥'.$out['freight'].'</p>
<h3>合计金额：<span style="color:#FF0000">￥'.number_format($out['total'], 2).'</span></h3>
 <h2 style="text-align:center">零件检验质量标准</h2>
';
$html .= '<p>'.htmlspecialchars_decode(nl2br($out['qs'])).'</p><h2 style="text-align:center">货物交付以及结算区域</h2>';
$html .= '<p>'.htmlspecialchars_decode(nl2br($out['qt'])).'</p>';
$pdf->writeHTML($html, true, false, true, false, '');

//$pdf->Output(public_path($rot.$rnd.'.pdf'), 'FI');
$pdf->Output(public_path($rnd.'.pdf'), 'I');
		return true;
	}
	
	
	public function GetFastorder(Request $request){
		$oid = urldecode($request->Input('val'));
		
		try {
			$oid = decrypt($oid);
			$pieces = explode("|", $oid);
			$oid = $pieces[1];
		} catch (DecryptException $e) {
			abort(403, '无订单信息');
		}
		$qut = Fastquotation::find($oid);
		if(empty($qut))abort(403, '无订单信息');

		$parts = Fastpart::whereIn('fastparts.id', json_decode($qut->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'fastparts.material_id')
						->select('fastparts.*','materials.name as matname')
						->get();
		
		$out = $qut->toArray();
		$out['parts'] = $parts->toArray();
		 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT,true, 'UTF-8', true);
		        // 设置文档信息
        // set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('TPM3D');
$pdf->SetTitle('TPM3D');
$pdf->SetSubject('TPM3D');
$pdf->SetKeywords('TPM3D, PDF');

// set default header data
$ad = TenantAd::where('tenant_id',$qut->tenat_id)->where('page','合同')->select('loca','imgurl')->get();
$adt = '';$adf = '';
if(count($ad) !=2 )$pfs = PlantFormSetting::first();
foreach($ad as $v){
	if($v['loca'] == 'foot')
		$adf = str_replace_first(env('APP_URL').'/storage/', '../../../../../storage/app/public/', $v['imgurl']);
	if($v['loca'] == 'top')
		$adt = str_replace_first(env('APP_URL').'/storage/', '../../../../../storage/app/public/', $v['imgurl']);
}
if($adt == '')$adt=str_replace_first(env('APP_URL'), '../../../../../public', $pfs->ad_header_url);
if($adf == '')$adf=str_replace_first(env('APP_URL'), '../../../../../public', $pfs->ad_footer_url);

$pdf->SetHeaderData($adt, 40, 'TPM3D', 'www.tpm3d.com', array(0,64,255), array(0,64,128));
$pdf->setFooterData($adf, 30, '', '', array(0,64,0), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('stsongstdlight', '', 12);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = '
<h1 style="text-align:center">报价单(合同)</h1>
<h2 style="text-align:center">零件清单</h2>
<p>报价单号 : '.$out['fast_id'].'<br/>
报价日期 : '.$out['created_at'].'</p>
<p>
';
$pdf->writeHTML($html, true, false, true, false, '');
$y = $pdf->getY()+5;
$x = 15;
$pc = count($out['parts']);
$pi = 0;
for($i = 0;$i<3;++$i){
	for($j = 0;$j<2;++$j){
		$pdf->SetXY($x,$y);
		$pdf->SetFont('', '', 12);
		$pdf->Cell(0, 6, $out['parts'][$pi]['name']);
		$pdf->SetXY($x,$y+6);
		$pdf->SetFont('', '', 10);
		$pdf->Cell(0, 5, $out['parts'][$pi]['matname']);
		$pdf->Rect($x, $y+11, 28, 20, 'F', [], array(216,216,216));
		$fitbox = 'C'.' ';
		$fitbox[1] = 'M';
		$imgdata = base64_decode(str_after($out['parts'][$pi]['diagram'], 'data:image/jpeg;base64,'));
		$pdf->Image('@'.$imgdata, $x, $y+11, 28, 20, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
		$pdf->SetXY($x+30,$y+11);
		$pv = json_decode($out['parts'][$pi]['volume_size'],true);
		$pdf->Cell(0, 5, '长'.$pv['xx'].'mm 宽'.$pv['yy'].'mm 高'.$pv['zz'].'mm');
		$pdf->SetXY($x+30,$y+16);
		$pdf->Cell(0, 5, '体积'.$pv['volume'].'mm3');
		$pdf->SetXY($x+30,$y+21);
		$pdf->Cell(0, 5, '数量: '.$out['parts'][$pi]['product_num'].'件  单价: ￥'.number_format($out['parts'][$pi]['price']*$out['parts'][$pi]['coefficient'], 2));
		$x = $x + 92;
		$pi++;
		if($pi>=$pc)break;
	}
	$y = $y+35;
	if($y > 248) {$y =  20;$pdf->AddPage();}
	$x=15;
	if($pi>=$pc)break;
	//if($i == 1){$y=$pdf->getY();}
}
// write the first column
$pdf->SetXY($x,$y);
$pdf->Ln(1);
$pdf->SetFont('', '', 12);
$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [216,216,216]));
$html = '
<p><hr /></p>
<h3>合计金额：<span style="color:#FF0000">￥'.number_format($out['total'], 2).'</span></h3>
<p></p>
<p><hr /></p>
<p></p>
<p></p>
 <h2 style="text-align:center">零件检验质量标准</h2>
';
$html .= '<p>'.htmlspecialchars_decode(nl2br($out['qs'])).'</p><h2 style="text-align:center">货物交付以及结算区域</h2>';
$html .= '<p>'.htmlspecialchars_decode(nl2br($out['qt'])).'</p>';
$pdf->writeHTML($html, true, false, true, false, '');

//$pdf->Output(public_path($rot.$rnd.'.pdf'), 'FI');
$pdf->Output('contract.pdf', 'D');
		return true;
	}
}
