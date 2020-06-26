function Create_Main_Menu_Goods(event,menu_id,menu_top_id,menu_top,menu_name,url) {
	document.getElementById("goodsdialogmenu").style.display="block";
	}

function Close_Dialog_Goods() {
	document.getElementById("goodsdialogmenu").style.display="none";
	}

function Close_Dialog1_Goods() {
	document.getElementById("costomaizer").style.display="none";
	}

function Change_Window_Goods(o, s) {
	console.log(s);
	document.getElementById("key_info_Goods").style.display="none";
	console.log(document.getElementById("main_info_Goods"));
	document.getElementById("main_info_Goods").style.display="none";
	document.getElementById("meta_info_Goods").style.display="none";
	document.getElementById("key_info_Materials").style.display="none";
	document.getElementById("meta_svaz_Goods").style.display="none";
	removeClass(document.getElementById("win_first_name_Goods"), "win_dialog_tab_selected");
	removeClass(document.getElementById("win_second_name_Goods"), "win_dialog_tab_selected");
	removeClass(document.getElementById("win_therd_name_Goods"), "win_dialog_tab_selected");
	removeClass(document.getElementById("win_four_name_materials"), "win_dialog_tab_selected");
	removeClass(document.getElementById("win_five_name_Goods"), "win_dialog_tab_selected");
	document.getElementById(s).style.display="block";
	console.log(s);
	console.log(document.getElementById(s));
	addClass(o, "win_dialog_tab_selected");
	}

function addClass_Goods(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	if (re.test(o.className)) return
	o.className = (o.className + " " + c).replace(/\s+/g, " ").replace(/(^ | $)/g, "")
	}

function removeClass_Goods(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	o.className = o.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "")
}
function Create_Main_Menu(event,menu_id,menu_top_id,menu_top,menu_name,url) {
	document.getElementById("dialogmenu").style.display="block";
	}

function Close_Dialog() {
	document.getElementById("dialogmenu").style.display="none";
	}

function Change_Window(o, s) {
	console.log(s);
	document.getElementById("key_info").style.display="none";
	document.getElementById("main_info").style.display="none";
	//document.getElementById("meta_info").style.display="none";
	removeClass(document.getElementById("win_first_name"), "win_dialog_tab_selected");
	//removeClass(document.getElementById("win_second_name"), "win_dialog_tab_selected");
	removeClass(document.getElementById("win_therd_name"), "win_dialog_tab_selected");
	document.getElementById(s).style.display="block";
	addClass(o, "win_dialog_tab_selected");
	}

function addClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	if (re.test(o.className)) return
	o.className = (o.className + " " + c).replace(/\s+/g, " ").replace(/(^ | $)/g, "")
	}

function removeClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	o.className = o.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "")
	}

function toDescription(oDom){
	if(!(!oDom)){
		var oParent=oDom.parentNode.parentNode.parentNode.parentNode.parentNode;
		if(!(!oParent)){
		var oTable=oParent.getChildById("keystable");
		if(!(!oTable)){
			var oTbody=oTable.childNodes[1];
			if(!(!oTbody)){
				console.log(oTbody);
				var sStr=oDom.getAttribute('data-string');
				var sFunction=oDom.getAttribute('data-function');
				var oTargetTR;
				for(var q in oTbody.childNodes){
				//for(var q=0; q<10; q++){
					if(!(!oTbody.childNodes[q].getAttribute)){
						if(oTbody.childNodes[q].getAttribute('data-string')==sStr){
							oTargetTR=oTbody.childNodes[q];
							break;
							}
						}
					}
				console.log(oTargetTR);	
				oParent=oParent.parentNode.parentNode;
				var oInput=oParent.getChildById("keyres");
				if(!(!oInput)){
					switch(sFunction){
						case "edit":
							var oIName=oParent.getChildById("keyname");
							var oIDesc=oParent.getChildById("keydesc");
							var aStrings=sStr.split(':');
							oIName.value=aStrings[1];
							oIDesc.value=aStrings[2];
							oInput.value=oInput.value.replace(sStr,"");
							oTargetTR.parentNode.removeChild(oTargetTR);
							break;
						case "delete":
							oInput.value=oInput.value.replace(sStr,"");
							oTargetTR.parentNode.removeChild(oTargetTR);
							break;
						}
					}
				}
			}
		}
	else
		console.log('Ќе существует '+oParent);
		}
	}

function AddDescription(oShowTable,oDomName,oDomDescription,oResultinput){
	var oKeyWords=new Object();
	oKeyWords['key']=translite(oDomName.value);
	oKeyWords['name']=oDomName.value;
	oKeyWords['description']=oDomDescription.value;
	var sStr=oKeyWords['key']+":"+oKeyWords['name']+":"+oKeyWords['description'];
	var oTr=document.createElement("tr");
	oTr.setAttribute('data-string',sStr);
	var oTd=document.createElement("td");
	oTd.innerHTML=oDomName.value;
	oTr.appendChild(oTd);
	var oTd=document.createElement("td");
	oTd.innerHTML=oDomDescription.value;
	oTr.appendChild(oTd);
	var oTd=document.createElement("td");
	var oDiv=document.createElement('div');
	oDiv.setAttribute('class','class_edit');
	oDiv.setAttribute('data-string',sStr);
	oDiv.setAttribute('data-function','edit');
	oDiv.setAttribute('onClick','toDescription(this)');
	oTd.appendChild(oDiv);
	oTr.appendChild(oTd);	
	var oTd=document.createElement("td");
	var oDiv=document.createElement('div');
	oDiv.setAttribute('class','class_delete');
	oDiv.setAttribute('data-string',sStr);
	oDiv.setAttribute('data-function','delete');
	oDiv.setAttribute('onClick','toDescription(this)');
	oTd.appendChild(oDiv);
	oTr.appendChild(oTd);
	var oTd=document.createElement("td");
	var oDiv=document.createElement('div');
	oDiv.setAttribute('class','class_enable');
	oDiv.setAttribute('data-string',sStr);
	oDiv.setAttribute('data-function','edit');
	oDiv.setAttribute('onClick','toDescription(this)');
	oTd.appendChild(oDiv);
	oTr.appendChild(oTd);
	oShowTable.childNodes[1].appendChild(oTr);
	var sDelimetr=(oResultinput.value=='')?"":"|||";
	oResultinput.value=oResultinput.value+sDelimetr+sStr;
	console.log(oResultinput.value);
	}
function SetEvents(){
			var oEventTag=new Object();
			oEventTag['IMG']=new Object();
			oEventTag['IMG']['onClick']='this.WindowShow()';
			document.body.ClearEvents("IMG",oEventTag);
			} 

function LiSelect(oDom){
	var oParent=oDom.parentNode;
	var aLi=oParent.getElementsByTagName("LI");
	console.log(aLi);
	for(var key in aLi){
		if(!(!aLi[key].getAttribute)){
			aLi[key].setAttribute("class","");
			if(aLi[key]==oDom)
				aLi[key].setAttribute("class","act");
			} 
		}
	var iMaterialId=oDom.getAttribute("data-material");
	var iCatalog=oDom.getAttribute("data-catalog");
	var iGood=oDom.getAttribute("data-good");
	var sDom=oDom.getAttribute("data-strtag");
	//console.log(sDom);
	var oContainer=document.getElementById(sDom);
	//console.log(oContainer);
	if(!(!iMaterialId))
		if(iMaterialId!=''){
			var sString="action=show&addon=catalog&editor=ajax&procedure=GoodMaterialShow&material="+iMaterialId+"&id_catalog="+iCatalog+"&goodid="+iGood; 
			ShowAjaxToDom(sString,oContainer,"admin/",true);
			}
	}
function SelectImages(oDom){
	if(!(!oDom)){
		var sSketch=oDom.src;
		var sFull=oDom.getAttribute("data-full");
		var sDom=oDom.getAttribute("data-sDom");
		var oFullImage=document.getElementById(sDom);
		oDom.parentNode.parentNode.setAttribute("class","active");
		//oDom.setAttribute("class","active");
		oFullImage.src=sFull;
		}
	}
	
	function OperationFromBasket(oDom){
		if(!(!oDom)){
			if(!(!oDom.getAttribute)){
				var sOperation=oDom.getAttribute("data-operation");
				var oParent=oDom.parentNode;
				var sTagName=oParent.tagName;
				if((!(!sTagName)) && (sTagName!='')){
					if(sTagName=='TD') {
						for(var i in oParent.childNodes){
							if(!(!oParent.childNodes[i].getAttribute)){
								var sTag=oParent.childNodes[i].tagName;
								if((!(!sTag)) && (sTag=='INPUT')){
									if(oParent.childNodes[i].getAttribute("type")=='text'){
										oCount=oParent.childNodes[i];
										//console.log(oCount);
										}
									}
								}
							}
						}
					}
				var oTR=oParent.parentNode;
				var sTagName=oTR.tagName;
				if((!(!sTagName)) && (sTagName!='') && (sTagName=='TR')){
					if(!(!oTR.getAttribute)){
						var sGoodList=oTR.getAttribute("data-goodlist");
						var sGood=oTR.getAttribute("data-good");
						if(sGoodList!='' && sGood!='' && sOperation!=''){
							var sPost="action=show&addon=basket&show=ajax&procedure=logic&do="+sOperation+"&sGoodList="+sGoodList+"&sGood="+sGood;
							aGlobal['sOperation']='gotobasket';
							var oBasket=document.getElementById("basketdiv");
							if(!(!oBasket)){
								//aGlobal['listen_basket']=sOperation;//нельз¤, так как очень сложно получить момент когда нужно переменную очистить
								switch(sOperation){
									case "update":
										sPost+="&iCount="+oCount.value;
										break;
									case "updatedays":
										aGlobal['days']=oCount.value;
										//sPost+="&days="+oCount.value;
										break;
									case "delete":
										
										break;
									}
								//var itTop=document.body.scrollTop;
								if(!(!oDom.getAttribute("data-sTarget"))){
									switch(oDom.getAttribute("data-sTarget")){
										case "price":
											//наличие \отсутвие лифта
											// этаж
											//км до када км после када
											var oTR=document.getElementById("KAD");
											var sDOST='';
											if(oTR){
												sDOST=oTR.getInputString();
												}
											aGlobal['sOperation']='SetDostavka';
											AjaxResult("action=show&addon=basket&show=ajax&procedure=logic&sOperation=SetDostavka&"+sDOST,"admin/",true,oAJAXTemplate['SendMysql']);
											aGlobal['sTarget']='bottom';
											aGlobal['sOperation']='gotobasket';
											document.getElementById("bottfield").AddAjax(sPost+"&"+sDOST,"admin/",true,true,true,false,true);
											return true;
											break;
										case "line":
											aGlobal['sTarget']='line';
											oDom.parentNode.parentNode.AddAjax(sPost,"admin/",true,true,true,false,true);
											aGlobal['sTarget']='bottom';
											document.getElementById("bottfield").AddAjax(sPost,"admin/",true,true,true,false,true);
											return true;
											break;
										case "bottom":
											aGlobal['sTarget']='bottom';
											document.getElementById("bottfield").AddAjax(sPost,"admin/",true,true,true,false,true);
											return true;
											break;
										} 
									}
								oBasket.AddAjax(sPost,"admin/",true,true,true,false,true);
								//document.body.scrollTop=itTop;
								}
							}
						}
					}
				}
			}
		}
	
	function SelectSelected(oDom){
		if(!(!oDom)){
				var oParent=oDom.parentNode;
				var iCatalog=oDom.getAttribute("data-icatalog");
				var oUL=oDom.parentNode;
				var oDiv1=oUL.parentNode;
				var oParent=oDiv1.parentNode;
				var aDiv=GetChildByAttribute('data-show','parent',oParent,false);
				for(var key in aDiv){
					console.log(aDiv[key]);
					if(aDiv[key].id=='select'+iCatalog){
						aDiv[key].style.display="block";
						}	
					else
						aDiv[key].style.display="none";
					}
				if(oParent){
					var oLi=oParent.getElementsByTagName("LI");
					for(var i=0;i<oLi.length;i++){
						if(oLi[i]==oDom)
							oLi[i].setAttribute("class","act");
						else	
							oLi[i].setAttribute("class","");
						}
					}
			}
		}
	function MouseOutStars(oDom){
		if(!(!oDom)){
			var oParent=oDom;
			if(!(!oParent)){
				var aStars=GetChildByAttribute('data-show','stars',oParent,false);
				for(var key in aStars){
					aStars[key].setAttribute("class",aStars[key].getAttribute("data-default"));						
					}
				}	
			}		
		}		
	
	function MouseOverStars(oDom){
		if(!(!oDom)){
			var oParent=oDom.parentNode;
			if(!(!oParent)){
				var aStars=GetChildByAttribute('data-show','stars',oParent,false);
				//console.log(aStars);
				var sSelect=oDom.getAttribute("data-select");
				if(sSelect=='false'){
					var sClassName='stardiv act';
					var sSetData='false';
					for(var key in aStars){
						aStars[key].setAttribute("data-select",sSetData);	
						aStars[key].setAttribute("class",sClassName);	
						if(aStars[key]==oDom){
							var sClassName='stardiv';
							}
						
						}
					oDom.setAttribute("data-select","true");	
					}
				}	
			}
		}
	
	function DeleteFromSelect(oDom){
		if(!(!oDom)){
			var aData=oDom.dataset;
			var sPost="action=show&addon=Sravnenietovarov&procedure=Show&";
			for(var key in aData){
				aGlobal[key]=aData[key];
				}
			document.getElementById("result").AddAjax(sPost,"admin/",true,true,true,false);
			
			}
		}
		
		function SetCount(oDom){
			var oParent=oDom.parentNode; 
			if(oParent.hasChildNodes()){
				for(var i = 0; i < oParent.childNodes.length; i++) {
					if(!(!oParent.childNodes[i].getAttribute)){
						if(oParent.childNodes[i].getAttribute('type')=='submit'){
							oParent.childNodes[i].setAttribute("data-count",oDom.value);
							}
						}
					}
				}
			}
	function SetCountWindow(oDom){
			var oParent=oDom.parentNode; 
			if(oParent.hasChildNodes()){
				//console.log(oParent.childNodes);
				for(var i = 0; i < oParent.childNodes.length; i++) {
					if(!(!oParent.childNodes[i].getAttribute)){
						if(oParent.childNodes[i].tagName=='SPAN'){
							//console.log(oParent.childNodes[i]);
							if(!(!oParent.childNodes[i].getAttribute("data-operation")) && oParent.childNodes[i].getAttribute("data-operation")=='basket'){
								oParent.childNodes[i].setAttribute("data-count",oDom.value);
								}
							//oParent.childNodes[i].setAttribute("data-count",oDom.value);
							}
						}
					}
				}
			}
	function AddToSelectGood(oDom){
				if(!(!oDom)){
					oDom.parentNode.setAttribute("class","sravnenie_act");
					oDom.parentNode.setAttribute("title","ѕерейти на страницу сравнени¤");
					var aData=oDom.dataset;
					var sPost="action=show&addon=catalog&procedure=good_add_select&";
					for(var key in aData){
						aGlobal[key]=aData[key];
						}
					var sResult=AjaxWaitResult(sPost,"admin/",true,false);
					oDom.setAttribute("onClick","");
					oDom.innerHTML=sResult;
					}
				
				}
	function ClickToSmallAddToSelect(oDom){
		if(!(!oDom)){
			oDom.setAttribute("class","span_sravnenie_act");
			oDom.setAttribute("onclick","MoveTOSelect();return false;");
			oDom.setAttribute("title","ѕерейти на страницу сравнени¤");
			var aData=oDom.dataset;
			var sPost="action=show&addon=catalog&procedure=good_add_select&";
			for(var key in aData){
				aGlobal[key]=aData[key];
				}
			var sResult=AjaxWaitResult(sPost,"admin/",true,false);
			}
		}
	function MoveTOSelect(){
		location.href = '/comparison';
		}
	function SpeekWithBasket(oDom){
				var sGoodList=oDom.getAttribute("data-goodlist");
				var sGood=oDom.getAttribute("data-good");
				var sOperation=oDom.getAttribute("data-operation");
				var fPrice=oDom.getAttribute("data-price");
				var sUrl=oDom.getAttribute("data-url");
				fPrice=(((fPrice.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,'')).replace(/\s+/g, ''));
				if(sGoodList!='' && sGood!='' && sOperation!=''){
					var oBasket=document.getElementById("basket");
					if(!(!oBasket)){
						var sPost="action=show&addon=basket&show=ajax&procedure=logic";
						aGlobal['sBasketGood']=sGood;
						aGlobal['sBasketGoodList']=sGoodList;
						aGlobal['sOperation']=sOperation;
						aGlobal['sBasketPrice']=fPrice;
						aGlobal['sUrl']=sUrl;
						aGlobal['listen_basket']='';
						aGlobal['iBasketeCount']=(!(!oDom.getAttribute("data-count")) && oDom.getAttribute("data-count")!='')?oDom.getAttribute("data-count"):1;
						oBasket.AddAjax(sPost,"admin/",true,true,true,false);
						if(sOperation=='basket'){
							aGlobal['sOperation']='';
							var oDiv=document.createElement("div");
							//aGlobal['sOperation']='bascketwindow';
							oDiv.AddAjax(sPost+"&sOperation=bascketwindow","admin/",true,true,true,false);
							oDiv.WindowShow();
							}
						if(sOperation=='oneclick'){
							aGlobal['sOperation']='';
							var oDiv=document.createElement("div");
							//aGlobal['sOperation']='basketonewindow';
							oDiv.AddAjax(sPost+"&sOperation=basketonewindow","admin/",true,true,true,false);
							oDiv.WindowShow();
							}
						}
					}
				}
			function UserNext(oDom){
				if(!(!oDom)){
					var sInputs=oDom.getInputString();
					console.log(sInputs);
					var sPost="action=show&addon=basket&show=ajax&procedure=logic&"+sInputs;
					aGlobal['sOperation']='gotobasket';
					var sResult=false;
					sResult=AjaxWaitResult(sPost,"admin/",true,true);
					if(!(!sResult)){
						var sPost="action=show&addon=basket&show=ajax&procedure=logic";
						aGlobal['sOperation']='byuIt';
						aGlobal['bOneClick']='true';
						var oWDom=document.createElement('DIV');
						oWDom.AddAjax(sPost,"admin/",true,true,true,false);
						oWDom.WindowShow();
						}
					//var oBasket=document.getElementById("basketdiv");
					//if(!(!oBasket))
					//	oBasket.AddAjax(sPost,"admin/",true,true,true,false);
					}
				//UserSubmit(oDom);
				}
			function UserFail(oDom){
				document.getElementById('fancyboxmain').style.display='none'; 
				var sPost="action=show&addon=basket&show=ajax&procedure=logic";
				//aGlobal['sBasketGood']=sGood;
				//aGlobal['sBasketGoodList']=sGoodList;
				aGlobal['sOperation']='removeuser';
				AjaxResult("admin/ajax.php",sPost,true,true);
				}
			function GoToUser(){
				var sPost="action=show&addon=basket&show=ajax&procedure=logic";
				aGlobal['sOperation']='gotouser';
				aGlobal['listen_basket']='';
				var oBasket=document.getElementById("basketdiv");
				if(!(!oBasket))
					oBasket.AddAjax(sPost,"admin/",true,true,true,false);
				}
			function UserSubmit(oDom){
				if(!(!oDom)){
					var sInputs=oDom.getInputString();
					console.log(sInputs);
					var sPost="action=show&addon=basket&show=ajax&procedure=logic&"+sInputs;
					aGlobal['sOperation']='gotobasket';
					var oBasket=document.getElementById("basketdiv");
					if(!(!oBasket))
						oBasket.AddAjax(sPost,"admin/",true,true,true,false);
					}
				}
			function GoToBasket(){
				var sPost="action=show&addon=basket&show=ajax&procedure=logic";
				aGlobal['sOperation']='gotobasket';
				aGlobal['listen_basket']='';
				var oBasket=document.getElementById("main");
				/*if(!(!oBasket))
					oBasket.AddAjax(sPost,"admin/",true,true,true,false);*/
				location.href = '/basket';
				}
			
			function BuyIt(oDom){
				var sPost="action=show&addon=basket&show=ajax&procedure=logic&";
				aGlobal['sOperation']='byuIt';
				aGlobal['bOneClick']='false';
				if(!(!oDom)){
					sPost+=oDom.getInputString();
					}
				var oDiv=document.createElement('DIV');
				oDiv.AddAjax(sPost,"admin/",true,true,true,false);
				oDiv.WindowShow();
				//AjaxResult("admin/ajax.php",sPost,true,true);
				//window.history.back();
				}
			function FormonClick(oDom){
					var sUrl="find";
					if(!(!oDom)){
						var oInput=oDom.getChildById("id");
						location.href = sUrl+'/'+oInput.value;
						}
					}
			function ShowMap(oDom){
				if(!(!oDom)){
					if(!(!oDom.getAttribute)){
						if(oDom.getAttribute("data-adress")){
							var oFrame=document.createElement("IFRAME");
							oFrame.src="https://www.google.com/maps/"+oDom.getAttribute("data-adress");
							oFrame.setAttribute("style","width:99%;height:99%;");
							oFrame.WindowShow();
							}
						}
					}
				}
		function ChangeDays(oDom){
			var oIDayCount=document.getElementById("iCountDays");
			var oDayStart=document.getElementById("date_start");
			var oDayStop=document.getElementById("date_stop");
			var iDaysCount=parseInt(oIDayCount.value);//количество дней
			var iCoutMS=iDaysCount*86400000;//количество м-секунд
			var iDayStartMS = Date.parse(oDayStart.value);//дата начала в м-секундах
			var iDayStopMS = Date.parse(oDayStop.value);//дата конца в м-секундах
			var iDayCount=iDayStopMS-iDayStartMS;//введеный узером промежуток в м-секундах
			switch(oDom){
				case oIDayCount://если юзер ввел количество дней
					if(!isNaN(iDayStartMS)){//если введена дата начала
						iDayStopMS=iDayStartMS+iCoutMS;//дата начала + количество дней
						var newDate = new Date(iDayStopMS);
						var currentISODateString = newDate.toISOString().replace('Z', ''); 
						oDayStop.value=currentISODateString;
						}
					break;
				case oDayStop:
					if(!isNaN(iDayStartMS)){
					var fDays=((iDayStopMS-iDayStartMS)/86400000);
					var iDays=Math.ceil(fDays);
					if(iDays<=0){
						iDayStopMS=iDayStartMS+86400000;
						var newDate = new Date(iDayStopMS);
						var currentISODateString = newDate.toISOString().replace('Z', ''); 
						oDayStop.value=currentISODateString;
						iDays=1;
						}
					//iDays=(iDays<=0)?1:iDays;
					oIDayCount.value=iDays;
					//oIDayCount.blur();
					OperationFromBasket(oIDayCount);
					//alert('1');
					break;
					}
				}
			console.log('iDayStartMS='+iDayStartMS);
			console.log('iDayStopMS='+iDayStopMS);
			console.log('iCoutMS='+iCoutMS);
			console.log(iDayStopMS-iDayStartMS);
			
			//console.log(ts);
			//var d = new Date(ts);
			}			
	function Admin_Catalog_ShowGoods(iCatalog){ 
		delete aGlobal;
		aGlobal=new Object;
		aGlobal['iLoadcount']=0
		aGlobal['scrollwhait']=true;
		aGlobal['action']='edit';
		aGlobal['addon']='catalog';
		aGlobal['idgoodslist']=iCatalog;
		var oDiv1=document.getElementById("goodslist"); 
		var oDiv2=document.getElementById("catalog"); 
		ShowAjaxToDomWithoutJS("&procedure=catalog_goods&now_page=0",oDiv1,"",true);
		ShowAjaxToDomWithoutJS("&procedure=catalog_mGoodsList&now_page=0",oDiv2,"",true);
		}		
	function Admin_Catalog_ApplyFilters(){
		var oParentDom=document.getElementById("catalog").parentNode;
		var Div2=document.getElementById("catalog"); 
		var sLimit=oParentDom.getChildById("limit").value;
		console.log(oParentDom.getChildById("limit")); 
		var sCostimizer=oParentDom.getChildById("costimizer").value; 
		var sGoodname=oParentDom.getChildById("goodname").value; 
		var oDiv1=document.getElementById("goodslist"); 
		var oDiv2=document.getElementById("catalog");
		aGlobal['limit']=sLimit;
		aGlobal['costimizer']=sCostimizer;
		aGlobal['goodname']=sGoodname;
		aGlobal['addon']='catalog';
		ShowAjaxToDomWithoutJS("&procedure=catalog_goods",oDiv1,"",true);
		ShowAjaxToDomWithoutJS("&procedure=catalog_mGoodsList",oDiv2,"",true);
		}	
	function Admin_Catalog_SetPage(iPage){
		var oDiv1=document.getElementById("goodslist"); 
		var oDiv2=document.getElementById("catalog"); 
		aGlobal['now_page']=iPage;
		ShowAjaxToDomWithoutJS("&procedure=catalog_goods",oDiv1,"",true);
		ShowAjaxToDomWithoutJS("&procedure=catalog_mGoodsList",oDiv2,"",true);
		}		
function SelectCatalogToRelatedGood(oDom){
	if(!(!oDom)){
		var iD=oDom.getAttribute("data-goodlist");
		aGlobal['iCatalogus']=iD;
		var oParent=oDom.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
		console.log(oParent);
		var oTarget=oParent.getChildById("waitajaxdiv");
		oTarget.InsertAjax("&procedure=catalog_FormRelatedGoodsGoods",'',true);
		}
	}	
function CheckRelatedGoods(oDom){
	if(!(!oDom)){
		var bOperation=oDom.checked;
		var sOperation=(bOperation)?"INSERT":"DELETE";
		var sTargetGood=oDom.getAttribute("data-targetGoods");
		var sRowId=oDom.getAttribute("data-rowid");
		AjaxResult("&procedure=catalog_FormRelatedGoodsGoods&targetGoods="+sTargetGood+"&rowid="+sRowId+"&operation="+sOperation,"",true);
		}
	}
	
function SelectCatalogInCalculator(oDom){
	if(!(!oDom)){
		iInput=oDom.selectedIndex;
		oDom=oDom.options[iInput];
		var iCatalog=oDom.value;
		console.log(iCatalog);
		if(iCatalog!='' && iCatalog!='Выбрать'){
			aGlobal['catalog']=iCatalog;
			aGlobal['action']='edit';
			aGlobal['addon']='kalikulyatortovarov';
			var oPanel1=document.getElementById("right_top");
			oPanel1.InsertAjax("&procedure=checkCatalog",'',true);
			var oPanel2=document.getElementById("main_panel");
			oPanel2.InsertAjax("&procedure=WorkWithCatalog",'',true);
			}
		}
	}
function SendCalkToMysql(oDom,oTarget){
	var oTarget=oTarget||false;
	if(!(!oDom)){
		aGlobal['action']='edit';
		aGlobal['addon']='kalikulyatortovarov';
		var oDataSet=oDom.dataset;
		var sPost="&procedure=MysqlWaiting&";
		for(var key in oDataSet){
			sPost+=key+"="+oDataSet[key]+"&";
			}
		if(!oTarget){
			AjaxResult(sPost,"",true);
			}
		else
			oTarget.InsertAjax(sPost+"&oTarget=true",'',true);
		}
	}
function OnDocumentLoadEvent(){
	//alert('onLoad');
	var oClassEvent={"operator":"samefunc","phone":"samefunc","calk":"samefunc"};
	for(var sClass in oClassEvent){
		var oElement=document.getElementsByClassName(sClass);
		if(oElement){
			for(var i=0;i<oElement.length;i++){
				if(oElement[i]){
					if(oElement[i].getAttribute){
						oElement[i].addEventListener("click",function(){console.log(this)});
						}
					}
				}
			}
		}
	}
function ShowUploadFile(oDom){
	if(oDom){
		var oDiv=document.getElementById("formupload");
		if(oDiv){
			oDiv.style.display=(oDom.checked)?"block":"none";
			}
		}
	}
function DeleteImage(oDom){
	if(oDom){
		var sDelited=oDom.getAttribute("data-value");
		aGlobal['img']=aGlobal['img'].replace(sDelited,"");
		oDom.parentNode.delete();
		}
	}
function InsertIMAGE(value,type){
	value="admin/"+value;
	aGlobal['img']+=((aGlobal['img']=='')?"":";")+value;
	var oDom=document.getElementById("photoUPloader");
	var aIMG=new Array('.gif','.jpg','.jpeg','.png','.tif','.GIF','.JPG','.JPEG','.PNG','.TIF');
	if(oDom){
		var sInner=(aIMG.in_array(type))?"<img src='"+value+"'>":type;
		oDom.innerHTML=oDom.innerHTML+"<div>"+sInner+"<div data-value='"+value+"' onClick='DeleteImage(this);'></div></div>";
		}
	
	}
	
function Spoilers(){
	aGlobal['spoiler']=new Object();
	
	var aTargets=document.body.getElementsByClassName("spoiler");
	if(aTargets){
		for(var i=0;i<aTargets.length;i++){
			if(aTargets[i]){
				aTargets[i].style.display="none";
				var  iNumber=aTargets[i].getAttribute("data-spoiler");
				aGlobal['spoiler'][iNumber]=aTargets[i];
				}
			}
		}
	var aButtons=document.body.getElementsByClassName("spoiler_show");
	if(aButtons){
		for(var q=0;q<aButtons.length;q++){
			if(aButtons[q]){
				var oDom=aButtons[q];
				console.log(oDom);
				oDom.addEventListener("click",function(){ShowSpoiler(oDom)});
				}
			}
		}
	}
	
function ShowSpoiler(oDom){
	var  iShowNumber=oDom.getAttribute("data-spoiler");
	
	if(aGlobal['spoiler'][iShowNumber]){
		switch(aGlobal['spoiler'][iShowNumber].style.display){
			case "block":
				aGlobal['spoiler'][iShowNumber].style.display="none";
				oDom.innerHTML=oDom.innerText.replace("Скрыть","Посмотреть");
				console.log(oDom.innerText);
				break;
			case "none":
				aGlobal['spoiler'][iShowNumber].style.display="block";
				oDom.innerText=oDom.innerText.replace("Посмотреть","Скрыть");
				console.log(oDom.innerText);
				break;
			}
		}
	}	
aGlobal['wait'].push('Spoilers');