(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-ee9d"],{"2iQr":function(t,e,i){"use strict";i.r(e);var a,n,r,s,u,o=i("gDS+"),l=i.n(o),c=i("FyfS"),d=i.n(c),p=i("7BsA"),h=i.n(p),m=(i("XhwK"),i("lD00")),f=i("ac2h"),v=i("uXAG"),g={data:function(){return{kusuPath:"",kusuOrderShengshow:!1,orderNum:"",customerNum:"",partNum:"",pc:!1,mo:!1,showKuSu:!1,kusuStatus:2,uploadLoading:!1,materialAllList:[],kusupartArr:[],kusupartshowTitle:"添加零件",kusuAllprice:0,qiyuShow:!1,qiyuData:{},kusuData:{},kusuaddpartshow:!1}},components:{CountTo:h.a},methods:{tofixeds:function(t,e,i){var a=Number(Number(t).toFixed(2));this.$set(e,i,a),this.kusuAllJSPrice()},kusuPathSure:function(){this.kusuOrderShengshow=!1,window.location.href=this.kusuPath},formatTooltip:function(t){return t/10},initAllMateria:function(){var t=this;Object(m.e)().then(function(e){t.materialAllList=[];e.data;var i=!0,a=!1,n=void 0;try{for(var r,s=d()(e.data);!(i=(r=s.next()).done);i=!0){var u=r.value,o=parseFloat(Number(u.price).toFixed(2)),l={};l.price=u.price*u.density,l.label=u.name+" (￥"+o+"/g )",l.value=u.id,t.materialAllList.push(l)}}catch(t){a=!0,n=t}finally{try{!i&&s.return&&s.return()}finally{if(a)throw n}}}).catch(function(t){console.log(t),console.log("获取材质列表失败")})},kusuSelectMate:function(t){var e=!0,i=!1,a=void 0;try{for(var n,r=d()(this.materialAllList);!(e=(n=r.next()).done);e=!0){var s=n.value;s.value==t&&(this.kusuData.price=s.price,this.kusuData.matname=s.label)}}catch(t){i=!0,a=t}finally{try{!e&&r.return&&r.return()}finally{if(i)throw a}}},showKuSuClick:function(){this.showKuSu=!0,this.kusupartArr=[],this.qiyuShow=!1,this.qiyuData={taxation:0,freight:0}},kusuaddpartSure:function(){this.kusuData.material_id?this.kusuData.product_num&&this.kusuData.product_num>=1?("添加零件"==this.kusupartshowTitle?(this.kusupartArr.push(this.kusuData),this.kusuaddpartshow=!1,this.kusuAllJSPrice()):(this.kusuaddpartshow=!1,this.kusuAllJSPrice()),this.qiyuShow=!0):this.$toast("请输入数量,最小为1"):this.$toast("请选择材质")},kusupartEdit:function(t){this.kusuData=t,this.kusuaddpartshow=!0,this.kusupartshowTitle="修改零件"},kusupartDel:function(t,e){var i=this;this.$dialog.confirm({title:"提示",message:"你确定要删掉该零件"}).then(function(){i.kusupartArr.splice(e,1),i.kusuAllJSPrice()}).catch(function(){})},kusuAllJSPrice:function(){this.qiyuData.taxation&&this.qiyuData.taxation>0||(this.qiyuData.taxation=0),this.qiyuData.freight&&this.qiyuData.freight>0||(this.qiyuData.freight=0),this.kusuAllprice=0;var t=!0,e=!1,i=void 0;try{for(var a,n=d()(this.kusupartArr);!(t=(a=n.next()).done);t=!0){var r=a.value,s=(Number(r.volume_size.volume)*Number(r.price)*Number(r.coefficient/10)/1e3).toFixed(2)*Number(r.product_num);this.kusuAllprice+=s}}catch(t){e=!0,i=t}finally{try{!t&&n.return&&n.return()}finally{if(e)throw i}}this.qiyuData&&(this.qiyuData.taxation&&(this.kusuAllprice=Number(this.qiyuData.taxation)+Number(this.kusuAllprice)),this.qiyuData.freight&&(this.kusuAllprice=Number(this.qiyuData.freight)+Number(this.kusuAllprice))),this.kusuAllprice=parseFloat(this.kusuAllprice.toFixed(2))},kusuBaojiadan:function(){var t=this,e={};(e=this.qiyuData).parts=JSON.parse(l()(this.kusupartArr));var i=!0,a=!1,n=void 0;try{for(var r,s=d()(e.parts);!(i=(r=s.next()).done);i=!0){var u=r.value;u.price=Number((Number(u.price)*Number(u.volume_size.volume)*Number(u.coefficient/10)/1e3).toFixed(2))}}catch(t){a=!0,n=t}finally{try{!i&&s.return&&s.return()}finally{if(a)throw n}}var o=this.$loading({lock:!0,text:"提交中，请等待",spinner:"el-icon-loading",background:"rgba(255, 255, 255, 1)",target:document.querySelector(".kusuOrderB")});Object(f.h)(e).then(function(e){t.kusuOrderShengshow=!0,t.kusuPath=e.data.path,t.$toast.success("报价单生成成功"),t.showKuSu=!1,o.close()}).catch(function(e){o.close(),t.$toast({message:e.response.data.message,type:"warning"})})},addOne:function(t){t.product_num=Number(t.product_num),t.product_num=t.product_num+1,this.kusuAllJSPrice()},reduceOne:function(t){t.product_num=Number(t.product_num),t.product_num=t.product_num-1,t.product_num<=0&&(t.product_num=1,this.$toast("最小值为1")),this.kusuAllJSPrice()},stlStartS:function(t){this.kusuStatus=t,this.kusupartshowTitle="添加零件";var e=document.getElementById("stlFile2");document.getElementById("stlFile2").value=null,e.click()},selectDate:function(){},stlStart:function(t){var e=(t.target||window.event.srcElement).value;if("stl"==e.substring(e.lastIndexOf(".")+1))if((u=document.getElementById("stlFile2").files[0]).size/1024<=51200){var i=u.name.split(".")[0];this.kusuData={},this.$set(this.kusuData,"name",i),this.$set(this.kusuData,"product_num",1);var a=this.materialAllList[0].value;this.$set(this.kusuData,"material_id",a),this.kusuSelectMate(a),this.initStl()}else this.$toast("文件要小于50MB");else alert("请选择STL格式文件！")},initStl:function(){this.uploadLoading=!0,(a=0==y?new THREE.OrthographicCamera(window.innerWidth/-2,window.innerWidth/2,window.innerHeight/2,window.innerHeight/-2,1,1e4):new THREE.PerspectiveCamera(_,800/600,1.5,1e4)).position.set(x,b,k),a.up.set(O,F,j),n=new THREE.Vector3(w,S,D),a.lookAt(n),(r=new THREE.Scene).fog=new THREE.Fog(16777215,1,1e4);var t=this;(new THREE.STLLoader).load(u,function(e){e.computeBoundingBox(),e.center();for(var i=new THREE.MeshPhongMaterial({color:8421504,specular:1118481,shininess:200}),s=new THREE.Mesh(e,i),u=0,o=(new THREE.Geometry).fromBufferGeometry(s.geometry),l=0;l<o.faces.length;l++){var c=o.faces[l].a,d=o.faces[l].b,p=o.faces[l].c,h=new THREE.Vector3(o.vertices[c].x,o.vertices[c].y,o.vertices[c].z),m=new THREE.Vector3(o.vertices[d].x,o.vertices[d].y,o.vertices[d].z),f=new THREE.Vector3(o.vertices[p].x,o.vertices[p].y,o.vertices[p].z);new THREE.Triangle(f,m,h);u+=z(f,m,h)}var v,g,y;v=e.boundingBox.max.x-e.boundingBox.min.x,g=e.boundingBox.max.y-e.boundingBox.min.y,y=e.boundingBox.max.z-e.boundingBox.min.z;var _=parseInt(g),x=parseInt(v),b=parseInt(y),k=Math.abs(parseInt(u)),A={xx:x,yy:_,zz:b,volume:k},q="长:"+x+"mm  宽:"+b+"mm  高:"+_+"mm",E=k+"mm³";t.$set(t.kusuData,"volume_size1",q),t.$set(t.kusuData,"volume_size2",E),t.$set(t.kusuData,"volume_size",A);s.castShadow=!0,s.receiveShadow=!0,r.add(s),function(t,e,i,r){var s=Math.max(3*r.boundingBox.max.x,3*r.boundingBox.max.y,3*r.boundingBox.max.z);(a=new THREE.PerspectiveCamera(45,4/3,.1,1e4)).position.set(0,0,s),a.up.set(1,0,1),a.up.set(O,F,j),n=new THREE.Vector3(w,S,D),a.lookAt(n)}(0,0,0,e),t.animate()}),r.add(new THREE.AmbientLight(3355443)),A(-1,1,1,16777215,1.35),A(1,-1,-1,16777215,1),(s=new THREE.WebGLRenderer({antialias:!0})).setClearColor(r.fog.color),s.setSize(800,600),s.gammaInput=!0,s.gammaOutput=!0,s.shadowMapEnabled=!0,s.shadowMapCullFace=THREE.CullFaceBack;new THREE.OrbitControls(a,s.domElement);window.addEventListener("resize",q,!1)},animate:function(){E(this)},seePicture:function(t){Object(v.a)({images:[t],asyncClose:!0})}},mounted:function(){this.initAllMateria(),/Android|webOS|iPhone|BlackBerry/i.test(navigator.userAgent)?(this.pc=!1,this.mo=!0):(this.pc=!0,this.mo=!1)}},y=1,_=45,x=200,b=200,k=200,w=0,S=0,D=0,O=0,F=1,j=0;function z(t,e,i){return 1/6*(t.x*e.y*i.z+i.x*t.y*e.z+e.x*i.y*t.z-i.x*e.y*t.z-e.x*t.y*i.z-t.x*i.y*e.z)}function A(t,e,i,a,n){var s=new THREE.DirectionalLight(a,n);s.position.set(t,e,i),r.add(s)}function q(){a.aspect=window.innerWidth/window.innerHeight,a.updateProjectionMatrix(),s.setSize(window.innerWidth,window.innerHeight)}function E(t){t.uploadLoading=!1;var e=new Image;s.render(r,a);var i=s.domElement.toDataURL("image/jpeg");e.src=i,i,t.kusuData.diagram=i,t.kusuaddpartshow=!0,s.render(r,a)}var V=g,N=(i("CWgq"),i("KHd+")),C=Object(N.a)(V,function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("el-row",[i("div",{staticStyle:{height:"100vh",width:"100%","background-color":"rgba(144,144,144,0.1)"}},[i("div",{staticStyle:{"text-align":"center","font-size":"1.2rem","font-weight":"bold","background-color":"#20A0FF",color:"#FFFFFF","line-height":"3rem","letter-spacing":"3px"}},[t._v(" 快速生成报价单 ")]),t._v(" "),i("div",{staticStyle:{"text-align":"center","margin-top":"2rem",color:"#4d4d4d","margin-bottom":"2rem","font-size":"18px","line-height":"25px"}},[t._v("\n\t\t\t\t您可以开始体验快速报价功能"),i("br"),t._v("\n\t\t\t\t请点击下方的按钮\n\t\t\t")]),t._v(" "),t.mo?i("el-row",{staticStyle:{"margin-bottom":"15px","text-align":"center"}},[i("el-button",{staticStyle:{"line-height":"1.5rem","font-size":"1.2rem",width:"60%"},attrs:{type:"success",round:""},on:{click:t.showKuSuClick}},[t._v("快速生成报价单")])],1):t._e(),t._v(" "),i("div",{staticStyle:{display:"flex","justify-content":"center","margin-top":"50px"}},[i("div",{staticStyle:{width:"22px"}},[i("div",{staticClass:"yuan1"},[t._v("1")]),t._v(" "),i("div",{staticClass:"xian1"}),t._v(" "),i("div",{staticClass:"yuan1"},[t._v("2")]),t._v(" "),i("div",{staticClass:"xian1"}),t._v(" "),i("div",{staticClass:"yuan1"},[t._v("3")])]),t._v(" "),i("div",{staticStyle:{"margin-left":"15px"}},[i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[t._v("体验用户")])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[t._v("您当前为体验用户(仅报价功能可用)")])]),t._v(" "),i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[i("a",{attrs:{href:"https://yp-dev.one2fit.cn/wechat/register"}},[t._v("商家入驻")])])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[i("a",{staticStyle:{"font-size":"13px",color:"#20B6F9"},attrs:{href:"https://yp-dev.one2fit.cn/wechat/register"}},[t._v("立即入驻")]),t._v("  使用平台完整功能,\n\t\t\t\t\t\t\t")])]),t._v(" "),i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[t._v("入驻升级")])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[t._v("报价单含有广告，入驻升级为VIP可去除")])])])])],1),t._v(" "),i("div",{staticClass:"quanping"},[i("van-actionsheet",{attrs:{title:"报价单"},model:{value:t.showKuSu,callback:function(e){t.showKuSu=e},expression:"showKuSu"}},[i("div",{staticStyle:{position:"relative"}},[t.uploadLoading?i("div",{staticStyle:{position:"absolute","z-index":"100",height:"100%",width:"100%",top:"120px",display:"flex","align-items":"center","justify-content":"center"}},[i("div",{staticStyle:{display:"flex","justify-content":"center","align-items":"center","background-color":"rgba(0,0,0,0.7)",padding:"20px","border-radius":"5px"}},[i("van-loading",{attrs:{type:"spinner",size:"40px"}}),t._v("  "),i("p",{staticStyle:{color:"#ffffff","vertical-align":"middle",padding:"0"}},[t._v("加载stl中")])],1)]):t._e(),t._v(" "),i("input",{staticStyle:{display:"none"},attrs:{type:"file",id:"stlFile2"},on:{change:function(e){t.stlStart(e)}}}),t._v(" "),i("div",{staticStyle:{"text-align":"center","margin-bottom":"15px","margin-top":"15px"}},[t._v("零件列表")]),t._v(" "),t._l(t.kusupartArr,function(e,a){return i("div",{staticStyle:{width:"94%",margin:"0 auto 15px",border:"1px solid #d6d6d6",padding:"5px","background-color":"#eeeeee"}},[i("van-row",[i("van-col",{attrs:{span:8}},[i("img",{staticStyle:{width:"100%",border:"1px solid #d6d6d6"},attrs:{src:e.diagram,alt:""},on:{click:function(i){t.seePicture(e.diagram)}}})]),t._v(" "),i("van-col",{staticStyle:{"padding-left":"10px"},attrs:{span:16}},[i("div",{staticStyle:{"font-size":"0.9rem","line-height":"1.2rem"}},[t._v(t._s(e.name))]),t._v(" "),i("div",{staticStyle:{"font-size":"0.9rem","line-height":"1.2rem"}},[t._v(t._s(e.matname))]),t._v(" "),i("div",{staticStyle:{"font-size":"0.9rem","line-height":"1.2rem","word-wrap":"break-word","word-break":"break-all"}},[t._v(t._s(e.volume_size1)+" ")]),t._v(" "),i("div",{staticStyle:{"font-size":"0.9rem","line-height":"1.2rem"}},[t._v("体积 :"+t._s(e.volume_size2))])])],1),t._v(" "),i("div",{staticStyle:{display:"flex","justify-content":"space-between",width:"100%","align-items":"center","border-bottom":"1px solid #d6d6d6","margin-bottom":"8px","padding-bottom":"5px","line-height":"1.5rem"}},[i("span",{staticStyle:{"font-size":"1rem","line-height":"1.2rem"}},[t._v("单价(元):\n\t\t\t\t\t\t\t\t"+t._s(parseFloat(Number(e.price)*Number(e.volume_size.volume)*Number(t.kusuData.coefficient/10)/1e3).toFixed(2))+"\n\t\t\t\t\t\t\t\t")]),t._v(" "),i("span",[t._v("数量  "),i("i",{staticClass:"el-icon-remove",staticStyle:{color:"#1478F0","font-size":"1.2rem"},on:{click:function(i){t.reduceOne(e)}}}),t._v(" "+t._s(e.product_num)+" "),i("i",{staticClass:"el-icon-circle-plus",staticStyle:{color:"#1478F0","font-size":"1.2rem"},on:{click:function(i){t.addOne(e)}}})])]),t._v(" "),i("div",{staticStyle:{display:"flex","justify-content":"space-between",width:"100%","align-items":"center"}},[i("span",{staticStyle:{"font-size":"1rem","line-height":"1.2rem"}},[t._v("小计:\n\t\t\t\t\t\t\t\t"+t._s((parseFloat(Number(e.price)*Number(e.volume_size.volume)*Number(t.kusuData.coefficient/10)/1e3).toFixed(2)*Number(e.product_num)).toFixed(2))+"\n\t\t\t\t\t\t\t\t")]),t._v(" "),i("span",[i("i",{staticClass:"el-icon-edit",staticStyle:{"font-size":"1.2rem",color:"#008800"},on:{click:function(i){t.kusupartEdit(e)}}}),t._v("      \n\t\t\t\t\t\t\t\t"),i("i",{staticClass:"el-icon-delete",staticStyle:{"font-size":"1.2rem",color:"#f56c6c"},on:{click:function(i){t.kusupartDel(e,a)}}}),t._v("    ")])])],1)}),t._v(" "),i("div",{staticStyle:{"text-align":"center"}},[i("el-button",{attrs:{type:"primary"},on:{click:function(e){t.stlStartS(2)}}},[t._v("添加零件(stl)")])],1),t._v(" "),t.qiyuShow?i("div",[i("div",{staticStyle:{display:"flex","align-items":"center","justify-content":"flex-start",width:"94%",margin:"15px auto"}},[t._v("\n\t\t\t\t\t\t\t\t税费(元)  "),i("el-input",{staticStyle:{width:"180px"},attrs:{type:"number",placeholder:"请输入数量"},on:{change:function(e){t.tofixeds(e,t.qiyuData,"taxation")}},model:{value:t.qiyuData.taxation,callback:function(e){t.$set(t.qiyuData,"taxation",e)},expression:"qiyuData.taxation"}})],1),t._v(" "),i("div",{staticStyle:{display:"flex","align-items":"center","justify-content":"flex-start",width:"94%",margin:"0 auto"}},[t._v("\n\t\t\t\t\t\t\t\t运费(元)  \t"),i("el-input",{staticStyle:{width:"180px"},attrs:{type:"number",placeholder:"请输入数量"},on:{change:function(e){t.tofixeds(e,t.qiyuData,"freight")}},model:{value:t.qiyuData.freight,callback:function(e){t.$set(t.qiyuData,"freight",e)},expression:"qiyuData.freight"}})],1)]):t._e(),t._v(" "),i("div"),t._v(" "),i("div",{staticStyle:{height:"2.5rem"}}),t._v(" "),i("div",{staticClass:"kusuOrderB",staticStyle:{background:"#FFFFFF",position:"fixed",bottom:"0",height:"2.5rem","line-height":"2.5rem","border-top":"1px solid #d6d6d6",width:"100%",display:"flex","justify-content":"space-between"}},[i("span",[t._v("  合计 : "+t._s(t.kusuAllprice.toFixed(2)))]),t._v(" "),i("el-button",{attrs:{type:"primary"},on:{click:t.kusuBaojiadan}},[t._v(" 生成报价单")])],1)],2)])],1),t._v(" "),i("div",{staticClass:"quanping"},[i("van-actionsheet",{attrs:{title:t.kusupartshowTitle},model:{value:t.kusuaddpartshow,callback:function(e){t.kusuaddpartshow=e},expression:"kusuaddpartshow"}},[i("div",{staticStyle:{"text-align":"center","margin-top":"15px"}},[i("img",{staticStyle:{border:"1px solid #d6d6d6",width:"85%",padding:"1px"},attrs:{src:t.kusuData.diagram,alt:""},on:{click:function(e){t.seePicture(t.kusuData.diagram)}}})]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("名称")]),t._v(" "),i("span",[i("el-input",{staticStyle:{width:"190px"},attrs:{placeholder:"请输入名称"},model:{value:t.kusuData.name,callback:function(e){t.$set(t.kusuData,"name",e)},expression:"kusuData.name"}})],1)]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("尺寸")]),t._v(" "),i("span",[t._v(t._s(t.kusuData.volume_size1))])]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("体积")]),t._v(" "),i("span",[t._v("体积 : "+t._s(t.kusuData.volume_size2))])]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("材质")]),t._v(" "),i("el-select",{staticStyle:{width:"190px"},attrs:{placeholder:"成型材质"},on:{change:t.kusuSelectMate},model:{value:t.kusuData.material_id,callback:function(e){t.$set(t.kusuData,"material_id",e)},expression:"kusuData.material_id"}},t._l(t.materialAllList,function(t){return i("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}))],1),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("数量")]),t._v(" "),i("el-input",{staticStyle:{width:"190px"},attrs:{type:"number",placeholder:"请输入数量"},model:{value:t.kusuData.product_num,callback:function(e){t.$set(t.kusuData,"product_num",e)},expression:"kusuData.product_num"}})],1),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("单价")]),t._v(" "),t.kusuData.volume_size?i("span",[t.kusuData.volume_size.volume&&t.kusuData.price?i("span",[t._v("\n\t\t\t\t\t\t\t"+t._s(parseFloat(Number(t.kusuData.price)*Number(t.kusuData.volume_size.volume)*Number(t.kusuData.coefficient/10)/1e3).toFixed(2))+"\n\t\t\t\t\t\t")]):t._e()]):t._e()]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("合计:")]),t._v(" "),t.kusuData.coefficient&&t.kusuData.product_num&&t.kusuData.price&&t.kusuData.volume_size.volume?i("span",[t._v("\n\t\t\t\t\t\t￥"+t._s((parseFloat(Number(t.kusuData.price)*Number(t.kusuData.volume_size.volume)*Number(t.kusuData.coefficient/10)/1e3).toFixed(2)*Number(t.kusuData.product_num)).toFixed(2))+"\n\t\t\t\t\t\t")]):t._e()]),t._v(" "),i("div",{staticClass:"kspdiv"},[i("span",[t._v("价格系数")]),t._v(" "),i("el-slider",{staticStyle:{width:"180px"},attrs:{"format-tooltip":t.formatTooltip,min:1,max:1e3},model:{value:t.kusuData.coefficient,callback:function(e){t.$set(t.kusuData,"coefficient",e)},expression:"kusuData.coefficient"}})],1),t._v(" "),i("div",{staticStyle:{"margin-top":"25px",margin:"15px","padding-right":"10px",display:"flex","justify-content":"flex-end"}},[i("el-button",{on:{click:function(e){t.kusuaddpartshow=!1}}},[t._v("取消")]),t._v(" "),i("el-button",{attrs:{type:"primary"},on:{click:t.kusuaddpartSure}},[t._v("确定")])],1)])],1),t._v(" "),i("div",{staticClass:"quanping"},[i("van-actionsheet",{attrs:{title:"报价单"},model:{value:t.kusuOrderShengshow,callback:function(e){t.kusuOrderShengshow=e},expression:"kusuOrderShengshow"}},[i("div",{staticStyle:{"text-align":"center",margin:"0 auto 1.5rem","font-size":"1.5rem","padding-top":"1.5rem"}},[t._v("快速报价单已生成")]),t._v(" "),i("div",{staticStyle:{"text-align":"center"}},[i("el-button",{staticStyle:{width:"70%","font-size":"1.1rem"},attrs:{type:"primary",round:""},on:{click:t.kusuPathSure}},[t._v("下载 (查看) 报价单(pdf)")])],1),t._v(" "),i("div",{staticStyle:{display:"flex","justify-content":"center","margin-top":"50px"}},[i("div",{staticStyle:{width:"22px"}},[i("div",{staticClass:"yuan1"},[t._v("1")]),t._v(" "),i("div",{staticClass:"xian1"}),t._v(" "),i("div",{staticClass:"yuan1"},[t._v("2")]),t._v(" "),i("div",{staticClass:"xian1"}),t._v(" "),i("div",{staticClass:"yuan1"},[t._v("3")]),t._v(" "),i("div",{staticClass:"xian1"}),t._v(" "),i("div",{staticClass:"yuan1"},[t._v("4")])]),t._v(" "),i("div",{staticStyle:{"margin-left":"15px"}},[i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[t._v("体验用户")])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[t._v("您当前为体验用户(仅报价功能可用)")])]),t._v(" "),i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[i("a",{attrs:{href:"https://yp-dev.one2fit.cn/wechat/register"}},[t._v("商家入驻")])])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[i("a",{staticStyle:{"font-size":"13px",color:"#20B6F9"},attrs:{href:"https://yp-dev.one2fit.cn/wechat/register"}},[t._v("立即入驻")]),t._v("  使用平台完整功能,\n\t\t\t \t\t\t\t")])]),t._v(" "),i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[t._v("入驻升级")])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"2rem"}},[t._v("报价单含有广告，入驻升级为VIP可去除")])]),t._v(" "),i("div",{staticStyle:{height:"22px","line-height":"22px"}},[i("span",{staticStyle:{"font-size":"15px"}},[t._v("温馨提示")])]),t._v(" "),i("div",{staticStyle:{height:"2rem"}},[i("span",{staticStyle:{"font-size":"13px",color:"#888888","line-height":"1.5rem"}},[t._v("\n\t\t\t\t\t\t\t部分浏览器仅能查看报价单\n\t\t\t\t\t\t"),i("br"),t._v("\n\t\t\t\t\t\t(请将链接复制到其他浏览器，即可自动下载)")])])])]),t._v(" "),i("div",{staticStyle:{width:"90%",margin:"2rem auto 0","line-height":"25px"}}),t._v(" "),i("div",{staticStyle:{position:"fixed",width:"100%",bottom:"25px",right:"10px",display:"flex","justify-content":"flex-end"}},[i("el-button",{attrs:{type:"primary"},on:{click:function(e){t.kusuOrderShengshow=!1}}},[t._v("关闭")])],1)])],1)])},[],!1,null,"99fffdc0",null);C.options.__file="index.vue";e.default=C.exports},"7BsA":function(t,e,i){t.exports=function(t){function e(a){if(i[a])return i[a].exports;var n=i[a]={i:a,l:!1,exports:{}};return t[a].call(n.exports,n,n.exports,e),n.l=!0,n.exports}var i={};return e.m=t,e.c=i,e.i=function(t){return t},e.d=function(t,i,a){e.o(t,i)||Object.defineProperty(t,i,{configurable:!1,enumerable:!0,get:a})},e.n=function(t){var i=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(i,"a",i),i},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="/dist/",e(e.s=2)}([function(t,e,i){var a=i(4)(i(1),i(5),null,null);t.exports=a.exports},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i(3);e.default={props:{startVal:{type:Number,required:!1,default:0},endVal:{type:Number,required:!1,default:2017},duration:{type:Number,required:!1,default:3e3},autoplay:{type:Boolean,required:!1,default:!0},decimals:{type:Number,required:!1,default:0,validator:function(t){return t>=0}},decimal:{type:String,required:!1,default:"."},separator:{type:String,required:!1,default:","},prefix:{type:String,required:!1,default:""},suffix:{type:String,required:!1,default:""},useEasing:{type:Boolean,required:!1,default:!0},easingFn:{type:Function,default:function(t,e,i,a){return i*(1-Math.pow(2,-10*t/a))*1024/1023+e}}},data:function(){return{localStartVal:this.startVal,displayValue:this.formatNumber(this.startVal),printVal:null,paused:!1,localDuration:this.duration,startTime:null,timestamp:null,remaining:null,rAF:null}},computed:{countDown:function(){return this.startVal>this.endVal}},watch:{startVal:function(){this.autoplay&&this.start()},endVal:function(){this.autoplay&&this.start()}},mounted:function(){this.autoplay&&this.start(),this.$emit("mountedCallback")},methods:{start:function(){this.localStartVal=this.startVal,this.startTime=null,this.localDuration=this.duration,this.paused=!1,this.rAF=(0,a.requestAnimationFrame)(this.count)},pauseResume:function(){this.paused?(this.resume(),this.paused=!1):(this.pause(),this.paused=!0)},pause:function(){(0,a.cancelAnimationFrame)(this.rAF)},resume:function(){this.startTime=null,this.localDuration=+this.remaining,this.localStartVal=+this.printVal,(0,a.requestAnimationFrame)(this.count)},reset:function(){this.startTime=null,(0,a.cancelAnimationFrame)(this.rAF),this.displayValue=this.formatNumber(this.startVal)},count:function(t){this.startTime||(this.startTime=t),this.timestamp=t;var e=t-this.startTime;this.remaining=this.localDuration-e,this.useEasing?this.countDown?this.printVal=this.localStartVal-this.easingFn(e,0,this.localStartVal-this.endVal,this.localDuration):this.printVal=this.easingFn(e,this.localStartVal,this.endVal-this.localStartVal,this.localDuration):this.countDown?this.printVal=this.localStartVal-(this.localStartVal-this.endVal)*(e/this.localDuration):this.printVal=this.localStartVal+(this.localStartVal-this.startVal)*(e/this.localDuration),this.countDown?this.printVal=this.printVal<this.endVal?this.endVal:this.printVal:this.printVal=this.printVal>this.endVal?this.endVal:this.printVal,this.displayValue=this.formatNumber(this.printVal),e<this.localDuration?this.rAF=(0,a.requestAnimationFrame)(this.count):this.$emit("callback")},isNumber:function(t){return!isNaN(parseFloat(t))},formatNumber:function(t){t=t.toFixed(this.decimals);var e=(t+="").split("."),i=e[0],a=e.length>1?this.decimal+e[1]:"",n=/(\d+)(\d{3})/;if(this.separator&&!this.isNumber(this.separator))for(;n.test(i);)i=i.replace(n,"$1"+this.separator+"$2");return this.prefix+i+a+this.suffix}},destroyed:function(){(0,a.cancelAnimationFrame)(this.rAF)}}},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i(0),n=function(t){return t&&t.__esModule?t:{default:t}}(a);e.default=n.default,"undefined"!=typeof window&&window.Vue&&window.Vue.component("count-to",n.default)},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=0,n="webkit moz ms o".split(" "),r=void 0,s=void 0;if("undefined"==typeof window)e.requestAnimationFrame=r=function(){},e.cancelAnimationFrame=s=function(){};else{e.requestAnimationFrame=r=window.requestAnimationFrame,e.cancelAnimationFrame=s=window.cancelAnimationFrame;for(var u=void 0,o=0;o<n.length&&(!r||!s);o++)u=n[o],e.requestAnimationFrame=r=r||window[u+"RequestAnimationFrame"],e.cancelAnimationFrame=s=s||window[u+"CancelAnimationFrame"]||window[u+"CancelRequestAnimationFrame"];r&&s||(e.requestAnimationFrame=r=function(t){var e=(new Date).getTime(),i=Math.max(0,16-(e-a)),n=window.setTimeout(function(){t(e+i)},i);return a=e+i,n},e.cancelAnimationFrame=s=function(t){window.clearTimeout(t)})}e.requestAnimationFrame=r,e.cancelAnimationFrame=s},function(t,e){t.exports=function(t,e,i,a){var n,r=t=t||{},s=typeof t.default;"object"!==s&&"function"!==s||(n=t,r=t.default);var u="function"==typeof r?r.options:r;if(e&&(u.render=e.render,u.staticRenderFns=e.staticRenderFns),i&&(u._scopeId=i),a){var o=Object.create(u.computed||null);Object.keys(a).forEach(function(t){var e=a[t];o[t]=function(){return e}}),u.computed=o}return{esModule:n,exports:r,options:u}}},function(t,e){t.exports={render:function(){var t=this,e=t.$createElement;return(t._self._c||e)("span",[t._v("\n  "+t._s(t.displayValue)+"\n")])},staticRenderFns:[]}}])},CWgq:function(t,e,i){"use strict";var a=i("J/Lw");i.n(a).a},"J/Lw":function(t,e,i){},XhwK:function(t,e,i){"use strict";i.d(e,"b",function(){return n}),i.d(e,"a",function(){return r}),i.d(e,"c",function(){return s}),i.d(e,"f",function(){return u}),i.d(e,"d",function(){return o}),i.d(e,"e",function(){return l}),i.d(e,"g",function(){return c});var a=i("t3Un");function n(t){var e=t;return Object(a.a)({url:"api/statistics/order",method:"post",data:e})}function r(t){var e=t;return Object(a.a)({url:"api/statistics/custome",method:"post",data:e})}function s(t){var e=t;return Object(a.a)({url:"api/statistics/part",method:"post",data:e})}function u(t){var e=t;return Object(a.a)({url:"api/statistics/order",method:"post",data:e})}function o(t){var e=t;return Object(a.a)({url:"api/statistics/custome",method:"post",data:e})}function l(t){var e=t;return Object(a.a)({url:"api/statistics/info",method:"post",data:e})}function c(t){var e=t;return Object(a.a)({url:"api/statistics/after",method:"post",data:e})}},ac2h:function(t,e,i){"use strict";i.d(e,"a",function(){return n}),i.d(e,"i",function(){return r}),i.d(e,"c",function(){return s}),i.d(e,"k",function(){return u}),i.d(e,"j",function(){return o}),i.d(e,"d",function(){return l}),i.d(e,"g",function(){return c}),i.d(e,"f",function(){return d}),i.d(e,"m",function(){return p}),i.d(e,"l",function(){return h}),i.d(e,"n",function(){return m}),i.d(e,"o",function(){return f}),i.d(e,"e",function(){return v}),i.d(e,"h",function(){return g}),i.d(e,"b",function(){return y}),i.d(e,"p",function(){return _}),i.d(e,"q",function(){return x});var a=i("t3Un");function n(t){return Object(a.a)({url:"api/sales/order/customlist",method:"get",params:t})}function r(t){return Object(a.a)({url:"api/sales/order/customlinkman/"+t,method:"get",params:""})}function s(t){return Object(a.a)({url:"api/sales/order/depusers/",method:"get",params:t})}function u(t){var e=t;return Object(a.a)({url:"api/sales/order/add",method:"post",data:e})}function o(t,e){return Object(a.a)({url:"api/sales/material/list/"+t,method:"get",params:e})}function l(t,e){return Object(a.a)({url:"api/sales/equipment/list/"+t,method:"get",params:e})}function c(t,e){return Object(a.a)({url:"api/sales/order/quotation/"+t,method:"get",params:e})}function d(t){return Object(a.a)({url:"api/sales/order/list",method:"get",params:t})}function p(t,e){var i=e;return Object(a.a)({url:"api/sales/order/edit/"+t,method:"post",data:i})}function h(t,e){return Object(a.a)({url:"api/sales/order/del/"+t,method:"get",params:e})}function m(t){var e=t;return Object(a.a)({url:"api/sales/order/quotation/chack",method:"post",data:e})}function f(t,e){var i=e;return Object(a.a)({url:"api/sales/order/quotation/update/"+t,method:"post",data:i})}function v(t){var e=t;return Object(a.a)({url:"api/sales/order/quotation/updateqrurl",method:"post",data:e})}function g(t){var e=t;return Object(a.a)({url:"api/sales/fastorder/add",method:"post",data:e})}function y(t){var e=t;return Object(a.a)({url:"api/fastorder/get",method:"post",data:e})}function _(t){var e=t;return Object(a.a)({url:"api/sales/order/reistparts",method:"post",data:e})}function x(t){var e=t;return Object(a.a)({url:"api/sales/order/reloadorder",method:"post",data:e})}},"gDS+":function(t,e,i){t.exports={default:i("oh+g"),__esModule:!0}},lD00:function(t,e,i){"use strict";i.d(e,"j",function(){return n}),i.d(e,"k",function(){return r}),i.d(e,"g",function(){return s}),i.d(e,"i",function(){return u}),i.d(e,"h",function(){return o}),i.d(e,"a",function(){return l}),i.d(e,"m",function(){return c}),i.d(e,"l",function(){return d}),i.d(e,"n",function(){return p}),i.d(e,"e",function(){return h}),i.d(e,"d",function(){return m}),i.d(e,"w",function(){return f}),i.d(e,"u",function(){return v}),i.d(e,"v",function(){return g}),i.d(e,"f",function(){return y}),i.d(e,"z",function(){return _}),i.d(e,"x",function(){return x}),i.d(e,"y",function(){return b}),i.d(e,"c",function(){return k}),i.d(e,"t",function(){return w}),i.d(e,"r",function(){return S}),i.d(e,"s",function(){return D}),i.d(e,"b",function(){return O}),i.d(e,"q",function(){return F}),i.d(e,"o",function(){return j}),i.d(e,"p",function(){return z});var a=i("t3Un");function n(t){return Object(a.a)({url:"api/info/list",method:"get",params:t})}function r(t){return Object(a.a)({url:"api/custype/list",method:"get",params:t})}function s(t){var e=t;return Object(a.a)({url:"api/info/add",method:"post",data:e})}function u(t,e){var i=e;return Object(a.a)({url:"api/info/edit/"+t,method:"post",data:i})}function o(t,e){return Object(a.a)({url:"api/info/del/"+t,method:"get",params:e})}function l(t,e){return Object(a.a)({url:"api/info/dellxr/"+t,method:"get",params:e})}function c(t){var e=t;return Object(a.a)({url:"api/info/linkman/default",method:"post",data:e})}function d(t){var e=t;return Object(a.a)({url:"api/info/cutsomer/cancelbind",method:"post",data:e})}function p(t){var e=t;return Object(a.a)({url:"api/info/deles",method:"post",data:e})}function h(t){return Object(a.a)({url:"api/fastorder/material/listall",method:"get",params:t})}function m(t){return Object(a.a)({url:"api/info/material/listall",method:"get",params:t})}function f(t){return Object(a.a)({url:"api/info/material/list",method:"get",params:t})}function v(t){var e=t;return Object(a.a)({url:"api/info/material/add",method:"post",data:e})}function g(t,e){var i=e;return Object(a.a)({url:"api/info/material/edit/"+t,method:"post",data:i})}function y(t){return Object(a.a)({url:"api/info/moldi/listall",method:"get",params:t})}function _(t){return Object(a.a)({url:"api/info/moldi/list",method:"get",params:t})}function x(t){var e=t;return Object(a.a)({url:"api/info/moldi/add",method:"post",data:e})}function b(t,e){var i=e;return Object(a.a)({url:"api/info/moldi/edit/"+t,method:"post",data:i})}function k(t){return Object(a.a)({url:"api/info/surface/listall",method:"get",params:t})}function w(t){return Object(a.a)({url:"api/info/surface/list",method:"get",params:t})}function S(t){var e=t;return Object(a.a)({url:"api/info/surface/add",method:"post",data:e})}function D(t,e){var i=e;return Object(a.a)({url:"api/info/surface/edit/"+t,method:"post",data:i})}function O(t){return Object(a.a)({url:"api/info/equipment/listall",method:"get",params:t})}function F(t){return Object(a.a)({url:"api/info/equipment/list",method:"get",params:t})}function j(t){var e=t;return Object(a.a)({url:"api/info/equipment/add",method:"post",data:e})}function z(t,e){var i=e;return Object(a.a)({url:"api/info/equipment/edit/"+t,method:"post",data:i})}},"oh+g":function(t,e,i){var a=i("WEpk"),n=a.JSON||(a.JSON={stringify:JSON.stringify});t.exports=function(t){return n.stringify.apply(n,arguments)}}}]);