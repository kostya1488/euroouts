oIconColums=document.createElement("IMG");
var oSlider=false;
var oParentSlider=false;
var iOldX=-1;
var iNowX=-1;
var oHead=false;
var aAdmin=new Object();//глобальна перемнная для админки 
var aGlobal=new Object();//глобальная переменная, передаваемая php через POST при вызове ShowAjaxToDom и AjaxResult если им указаны bReadGlobal
var aMetha=new Array();
var oScripts=Object();	
aGlobal['wait']=new Array();
aGlobal['iLoadcount']=0;
var oAJAXTemplate=new Object();//готовые шаблоны для вызова аякса
oAJAXTemplate['SendMysql']={'bAsynchronously':true};//стандартный вызвов - для передачи мускулу значния "на ходу" без возвратов для AjaxResult
oAJAXTemplate['SendReadMysql']={'bClearTags':true}; //передача мускулу значениея на ходу, с ожидаемым ответом по типу "true", "false", или числа для AjaxResult
oAJAXTemplate['DrawHTML']={'bAsynchronously':true,'bClearTags':false,'bReplace':true,'bReadScript':false};//без скриптов
oAJAXTemplate['DrawSratic']={'bAsynchronously':true,'bClearTags':false,'bReplace':true,'bReadScript':true};//стандартный используемый вызов для перерисовки содержимого элемента
oAJAXTemplate['DrawWaitSratic']={'bAsynchronously':false,'bClearTags':false,'bReplace':true,'bReadScript':true};//стандартный используемый вызов для перерисовки содержимого элемента с ожиданием
oAJAXTemplate['AddHTML']={'bAsynchronously':true,'bClearTags':false,'bReplace':false,'bReadScript':true};//стандартный используемый вызов для дозаписи содержимого элемента - используется в каталоге и  альбомах
oAJAXTemplate['AddHTMLFirst']={'bAsynchronously':false,'bClearTags':false,'bReplace':false,'bReadScript':true,'bFirst':true,'bHiddenRound':true};// используется только в альбомах
oAJAXTemplate['AlbomUpdate']={'bAsynchronously':false,'bClearTags':false,'bReplace':true,'bReadScript':true};// используется только в альбомах
/////////////////////////////////////////////ПРОТОТИПЫ//////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////ДИНАМИЧЕСКИЕ ЭЛЕМЕНТЫ//////////////////////////////////////////////////////////////////
HTMLElement.prototype.CheckForm=function(bAllRequired,oDom){
		
		var bAllRequired=bAllRequired||false;
		var oDom=oDom||this;
		var aRead=oDom.ReadInputs();
		var aPost=aRead['array'];
		var aInputs=aRead['object'];
		var sPost=aRead['string'];
		var bReturn=true;
		for(var sPostName in aPost){
			aInputs[sPostName].setAttribute("class","");
			if(!aPost[sPostName] || aPost[sPostName]==''){
				if(aInputs[sPostName].required || bAllRequired){//если это поле обязательно для заполния или все поля
					bReturn=false;
					aInputs[sPostName].setAttribute("class","act");
					aInputs[sPostName].setAttribute("placeholder","заполните это поле");
					}
				}
			if(!(!aInputs[sPostName].getAttribute("data-type"))){
				switch(aInputs[sPostName].getAttribute("data-type")){
					case "phone":
						var re = /^\d[\d\(\)\ -]{5,13}\d$/;
						var sPhone =aInputs[sPostName].value;
						var bValid = re.test(sPhone);
						////console.log(sPhone);
						if(!bValid){
							bReturn=false;
							aInputs[sPostName].setAttribute("class","act");
							aInputs[sPostName].value='';
							aInputs[sPostName].setAttribute("placeholder","телефон указан не верно");
							}
						break;
					case "mail":
						var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
						var sPhone =aInputs[sPostName].value;
						var bValid = re.test(sPhone);
						////console.log(sPhone);
						if(!bValid){
							bReturn=false;
							aInputs[sPostName].setAttribute("class","act");
							aInputs[sPostName].value='';
							aInputs[sPostName].setAttribute("placeholder","почта указана не верно");
							}
						break;
					case "password":
						if(aInputs[sPostName].value.length<5){
							bReturn=false;
							aInputs[sPostName].setAttribute("class","act");
							aInputs[sPostName].value='';
							aInputs[sPostName].setAttribute("placeholder","пароль не может быть меньше 5 символов");
							}
						break;
					}
				}
			if(!(!aInputs[sPostName].getAttribute("data-conf"))){
				var oConf=aInputs[aInputs[sPostName].getAttribute("data-conf")];
				if(!(!oConf)){
					//console.log(oConf);
					if(aInputs[sPostName].value!=oConf.value){
						aInputs[sPostName].setAttribute("class","act");
						oConf.setAttribute("class","act");
						aInputs[sPostName].value='';
						aInputs[sPostName].setAttribute("placeholder","пароли не совпадают");
						}
					}
				}	
			}
		
		return bReturn;
		}
function ShowFrame(sPost){
	var oDom=document.createElement("DIV");
	oDom.WindowShow();
	oDom.InsertFrame(sPost);
	}
HTMLElement.prototype.InsertFrame=function(sPost,oDom){
	var oDom=oDom||this;
	var oFrame=document.createElement("IFRAME");
	oFrame.setAttribute("style","display:block;position:relative;top:0%;left:0px;width:100%;height:100%");
	oFrame.src="admin/ajax.php?index=frame&"+sPost;
	oDom.appendChild(oFrame);
	//oFrame.WindowShow();
	}
HTMLElement.prototype.InsertAjax=function(sPost,sPrefix,bReadGlobal,oParam,oDom){
	var oParam=oParam||oAJAXTemplate['DrawSratic'];
	var oDom=oDom||this;
	var bReadGlobal=bReadGlobal||false;//по умолчанию - не читаем глобальные+
	var sPrefix=sPrefix||''; //обращаемся из "админа"+
	var bClearTags=oParam['bClearTags']||false; //не чистим теги
	var bReplace=oParam['bReplace']||false; //дозаписываем результат
	var bAsynchronously=oParam['bAsynchronously']||false; //ждем ответа до выполнения следущих скрпитов
	var bReadScript=oParam['bReadScript']||false;//не читаем скрипты
	var bFirst=oParam['bFirst']||false;//добавляем в конец
	var bHiddenRound=oParam['bHiddenRound']||false;//по умолчанию показывать крутящийся кружок во время ожидания ответа+
	var bRetainrMeta=oParam['bRetainrMeta']||false;//оставлять тег Мета
	var bScroll=oParam['bScroll']||false;//оставлять тег Мета
	bReadScript=(bClearTags)?false:bReadScript;//если чистим теги - не читаем скрипты
	var bValue=(oDom.tagName=="INPUT" || oDom.tagName=="TEXTAREA" )?true:false;//пишем в innerHTML или в value
	if(bScroll){
		var iScroll=oDom.scrollTop;
		}
	sPost=(bReadGlobal)?sPost+"&"+ReadGlobal():sPost;
	if(bReplace){
		if(bValue)
			oDom.value='';
		else
			oDom.innerHTML='';
		
		}
	//console.log(sPost);
	if(!bHiddenRound && !bValue){
		//создаем и отрисовываем кружок
		var oTempDiv=document.createElement("DIV");
		oTempDiv.setAttribute("style",'position:relative;width:33px;height:33px;background:url("'+sPrefix+'sprites/loading.gif");background-position:-33px -33px;');
		oTempDiv.id='ShowSmallTempDiv';
		oDom.appendChild(oTempDiv);
		}
	if(!bAsynchronously){//синхронный запрос
		var sСomeHtml=WaitFromAjax(sPost,sPrefix,bReadGlobal);//получили HTML
		AjaxResultProcessing(sСomeHtml,bClearTags,bReadScript,bRetainrMeta,bValue,oDom,bFirst,oTempDiv);//Обработка результата вынесена
		if(bScroll){
			oDom.scrollTop=iScroll; 
			}
		}
	else{//Асинхронный запрос
		var http = createRequestObject();
		if( http ){
			var sURL=sPrefix+"ajax.php";
			http.open("POST", sURL, true); 
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function(){
				if(http.readyState == 4 && http.status == 200) {
					var sСomeHtml = http.responseText;
					AjaxResultProcessing(sСomeHtml,bClearTags,bReadScript,bRetainrMeta,bValue,oDom,bFirst,oTempDiv);//Обработка результата вынесена
					if(bScroll){
						oDom.scrollTop=iScroll; 
						}
					}
				}
			http.send(sPost);
			}
		}
	}
function AjaxResultProcessing(sСomeHtml,bClearTags,bReadScript,bRetainrMeta,bValue,oDom,bFirst,oTempDiv){//обработка для синхронного и асинхронного результата аякса, пришедшего для записи
	sСomeHtml=(bClearTags)? (sСomeHtml.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sСomeHtml;//очистили теги
	if(bReadScript){//прочитали скрипты, вытащили оставшийся html
		if(sСomeHtml.indexOf('<script>') + 1) {
			var oScript=RecurseFindScript(sСomeHtml);
			if(oScript['script']!=''){
				sСomeHtml=oScript['html'];
				}						
			}
		}
	if(!bRetainrMeta){	//удалили metha
		if(sСomeHtml.indexOf('<meta') + 1) {
			var iPos1=sСomeHtml.indexOf('<meta');
			var iPos2=sСomeHtml.indexOf('>',iPos1)+1; 
			var sMetha=sСomeHtml.substring(iPos1,iPos2);
			sСomeHtml=sСomeHtml.replace(sMetha,""); 
			}
		}
	if(bValue){//если input
		oDom.value=(bFirst)?sСomeHtml+oDom.value:oDom.value+sСomeHtml;//напоминанию, что очистка могла произойти выше по тегу bReplace
		}
	else{//если innerHTML
		if(!(!oTempDiv)){
			var oParent=oTempDiv.parentNode;
			if(!(!oParent)){
				oParent.removeChild(oTempDiv);//удалили кружок
				}
			}
		//console.log(oDom);
		//console.log(sСomeHtml);
		if(bFirst)
			oDom.innerHTML=sСomeHtml+oDom.innerHTML;
		else
			oDom.innerHTML+=sСomeHtml;
		if(!(!oDom.onchange)){
			oDom.onchange();
			}
		}
	if(!(!oScript)){//запустили скрипты
		if(!(!oScript['script'])){
			RecurseAddScript(oScript['script']);
			}
		}
	}
	HTMLElement.prototype.AddAjax=function(sPost,sPrefix,bReadGlobal,bReplace,bReadScript,bClearTags,bScroll,oDom){
		//console.log()
		var oDom=oDom||this;
		var bReplace=bReplace||false;
		var bReadScript=bReadScript||false;
		var bReadGlobal=bReadGlobal||false;
		var bClearTags=bClearTags||false;
		var bScroll=bScroll||false;//оставлять тег Мета
		bReadScript=(bClearTags)?false:bReadScript;
		if(bScroll){
			var iScroll=document.body.scrollTop;
			}
		//console.log(bReadScript);
		//bReadScript=false;
		var sPrefix=sPrefix||'';
		var bValue=(oDom.tagName=="INPUT")?true:false;
		sPost=(bReadGlobal)?sPost+"&"+ReadGlobal():sPost;
		if(bReplace)
			if(bValue)
				oDom.value='';
			else
				oDom.innerHTML='';
		if(!bValue){
			var oTempDiv=document.createElement("DIV");
			oTempDiv.setAttribute("style",'position:relative;width:33px;height:33px;background:url("'+sPrefix+'sprites/loading.gif");background-position:-33px -33px;');
			oTempDiv.id='ShowSmallTempDiv';
			oDom.appendChild(oTempDiv);
			}
		//console.log(sPost);
		var http = createRequestObject();
		if( http ){
			var sURL=sPrefix+"ajax.php";
			http.open("POST", sURL, true); 
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function(){
				if(http.readyState == 4 && http.status == 200) {
					var sResult = http.responseText;
					sResult=(bClearTags)? (sResult.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sResult;
					if(bReadScript){
						if(sResult.indexOf('<script>') + 1) {
							var oScript=RecurseFindScript(sResult);
							if(oScript['script']!=''){
								RecurseAddScript(oScript['script']);
								sResult=oScript['html'];
								}						
							}
						}
					//console.log(sResult);
					if(!bValue){
						if(!(!oTempDiv)){
							var oParent=oTempDiv.parentNode;
							oTempDiv.parentNode.removeChild(oTempDiv);
							}
						if(bReplace)
							oDom.innerHTM='';
						oDom.innerHTML+=sResult;
						}
					else{
						if(bReplace)
							oDom.value='';
						oDom.value+=sResult;
						oDom.onchange();
						}
					if(bScroll){
						document.body.scrollTop=iScroll; 
						}
					}
				}
			http.send(sPost);
			//console.log('send='+sPost);
			}
		}
	HTMLElement.prototype.WriteFromAjax=function(sPost,sPrefix,bReadGlobal,bReplace,bReadScript,bClearTags,bFirst,oDom){
		//console.log(sPost); 
		//console.log('WaitAjax'); 
		var oDom=oDom||this;
		var bReplace=bReplace||false;
		var bReadScript=bReadScript||false;
		var bReadGlobal=bReadGlobal||false;
		var bClearTags=bClearTags||false;
		var bFirst=bFirst||false;
		bReadScript=(bClearTags)?false:bReadScript;
		var sPrefix=sPrefix||'';
		var bValue=(oDom.tagName=="INPUT")?true:false;
		sPost=(bReadGlobal)?sPost+"&"+ReadGlobal():sPost;
		if(bReplace)
			if(bValue)
				oDom.value='';
			else
				oDom.innerHTML='';
		if(!bValue){
			/*var oTempDiv=document.createElement("DIV");
			oTempDiv.setAttribute("style",'position:relative;width:33px;height:33px;background:url("'+sPrefix+'sprites/loading.gif");background-position:-33px -33px;');
			oTempDiv.id='ShowSmallTempDiv';
			oDom.appendChild(oTempDiv);*/
			}
		//console.log(sPost);
		var http = createRequestObject();				
		if( http ) {
			var sURL=sPrefix+"ajax.php";
			http.open("POST", sURL, false); 
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(sPost);
			var sResult=http.responseText;
			sResult=(bClearTags)? (sResult.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sResult;
			if(bReadScript){
				if(sResult.indexOf('<script>') + 1) {
					var oScript=RecurseFindScript(sResult);
					if(oScript['script']!=''){
						////RecurseAddScript(oScript['script']);
						sResult=oScript['html'];
						}						
					}
				}
			if(sResult.indexOf('<meta') + 1) {
				var iPos1=sResult.indexOf('<meta');
				var iPos2=sResult.indexOf('>',iPos1)+1; 
				var sMetha=sResult.substring(iPos1,iPos2);
				sResult=sResult.replace(sMetha,""); 
				}
			//console.log(sResult);
			if(!bValue){
				/*if(!(!oTempDiv)){
					var oParent=oTempDiv.parentNode;
					oTempDiv.parentNode.removeChild(oTempDiv);
					}*/
				if(bReplace)
					oDom.innerHTM='';
				if(bFirst)
					oDom.innerHTML=sResult+oDom.innerHTML;
				else
					oDom.innerHTML+=sResult;
				}
			else{
				if(bReplace)
					oDom.value='';
				oDom.value+=sResult;
				oDom.onchange();
				}
			if(!(!oScript)){
				if(!(!oScript['script'])){
					RecurseAddScript(oScript['script']);
					}
				}
			
			} 
		}
	Array.prototype.in_array = function(p_val) {
			for(var i = 0, l = this.length; i < l; i++)  {
				if(this[i] == p_val) {
					return true;
				}
			}
			return false;
		}

	HTMLElement.prototype.AjaxToProperty=function(params,oVariable,prefix,oDom){
		var oDom=oDom||this;
		var prefix=prefix||'';
		var http = createRequestObject();				
			if( http ) {
				http.open("POST", prefix+"ajax.php", true); 
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.onreadystatechange = function() {//Call a function when the state changes.
				if(http.readyState == 4 && http.status == 200) {
					var result = http.responseText;
					result=(result.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,'');
					oDom.setAttribute(oVariable,result);				
					}	
				}
				http.send(params);
			}
		}	
	/////ПЕРЕВОД////
	function is_object(mixed_var ){ 
		if(mixed_var instanceof Array) {
			return true;
			} 
		else{
			return (mixed_var !== null) && (typeof( mixed_var ) == 'object');
			}
		}

	function ObjectToString(oObject){
		var sResult='';
		for(var key in oObject){
			if(is_object(oObject[key]) || Array.isArray(oObject[key])){
				var sKey=key.escape_string();
				sResult+=((sResult=='')?"":" ")+sKey+"=("+ObjectToString(oObject[key])+")";
				}
			else{
				var sKey=key.toString().escape_string();
				var sValue=oObject[key].toString();
				var sValue=sValue.escape_string();
				sResult+=((sResult=='')?"":" ")+sKey+"="+sValue;
				}
			}
		return  sResult;
		}
	/////ПЕРЕВОД////
	//////////////////////////////////////////////////СОБЫТИЯ//////////////////////////////////////////////////////////////////////
	HTMLElement.prototype.ClearEvents=function(sTag,oSetTagEvent,oDiv){ 
		//oSetTagEvent['tagname']['event']='function';
		//oSetTagEvent['IMG']['onClick']='this.WindowShow()';
		//oSetTagEvent['INPUT']['onClick']='DrawScroll()';
		var oDiv=oDiv||this;
		var oSetTagEvent=oSetTagEvent||false;
		var sTag=sTag||"*";
		if(!(!oDiv)){
			var aImg=oDiv.getElementsByTagName(sTag);
			for(var key in aImg){
				if(!(!aImg[key].getAttribute)){ 
					if(aImg[key].parentNode.tagName!='A'){
						//console.log(aImg[key].parentNode.tagName);
						if(aImg[key].getAttribute("onmousedown")!=''){
							aImg[key].setAttribute("onmousedown",'');
							aImg[key].setAttribute("onmouseup",'');
							aImg[key].setAttribute("onmousemove",'');
							aImg[key].setAttribute("ontouchstart",'');
							aImg[key].setAttribute("ontouchmove",'');
							aImg[key].setAttribute("ontouchmove",'');
							aImg[key].setAttribute("ontouchend",'');
							var sTagName=aImg[key].tagName;
							if((!(!sTagName)) && (sTagName!='')){
								if(!(!oSetTagEvent[sTagName])){
									for(var eventname in oSetTagEvent[sTagName]){
										aImg[key].setAttribute(eventname,oSetTagEvent[sTagName][eventname]);
										}
									
									}
								}
							}
						}
					}
				}
			}
		}
	HTMLElement.prototype.getInputString=function(oDom){
		var oDom=oDom||this;
		var aRead=oDom.ReadInputs();
		return aRead['string'];
		}
		
	HTMLElement.prototype.getInputArray=function(oDom){
		var oDom=oDom||this;
		var aRead=oDom.ReadInputs();
		return aRead['array'];
		}
	HTMLElement.prototype.getInputObject=function(oDom){
		var oDom=oDom||this;
		var aRead=oDom.ReadInputs();
		return aRead['object'];
		}
	HTMLElement.prototype.ClearInputs=function(oDom){  
		var oDom=oDom||this;
		var aInputs=oDom.getElementsByTagName("INPUT");
		for(var key in aInputs){
			if(!(!aInputs[key].getAttribute)){
				aInputs[key].value='';
				}
			}
		var aInputs=oDom.getElementsByTagName("textarea");
		for(var key in aInputs){
			if(!(!aInputs[key].getAttribute)){
				aInputs[key].value=''; 
				}
			}
		}
	Object.defineProperty( Object.prototype, 'size', {
		value: function() { 
			var iLength = 0;
			for (var key in this){
				iLength++;
				}
			return iLength;
		},
		enumerable: false
	});	

	/*
	Так не надо. Функция добавляется в наследники объекта. Оставлю тут для напоминания
	Object.prototype.length=function(oObject){
		var oObject=oObject||this;
		var iLength = 0;
		for (var key in oObject){
			if(key!='length' && key!='size' && key!='lenght')
			iLength++;
			}
		return iLength;
		}
	Object.prototype.size=function(oObject){
		var oObject=oObject||this;
		return oObject.length();
		}
	Object.prototype.lenght=function(oObject){
		var oObject=oObject||this;
		return oObject.length();
		}*/
	HTMLElement.prototype.ReadInputs=function(oDom){  
		var oDom=oDom||this;
		var oResult=new Object();
		oResult['string']='';
		var oInputs=new Object;
		var oDoubleChecked=new Object;
		var oDoms=new Object;
		var aInputs=oDom.getElementsByTagName("DIV");
		for(var key in aInputs){
			if(!(!aInputs[key].getAttribute)){
				if(!(!aInputs[key].getAttribute("data-type"))){
					switch(aInputs[key].getAttribute("data-type")){
						case "input":
							oInputs[aInputs[key].id]=aInputs[key].innerText;
							oDoms[aInputs[key].id]=aInputs[key];
							break;
						}
					}
				}
			}
		var aInputs=oDom.getElementsByTagName("textarea");
		for(var key in aInputs){
			if(!(!aInputs[key].getAttribute)){
				var sTextareaText=aInputs[key].value;
				sTextareaText=(sTextareaText=='')?aInputs[key].innerText:sTextareaText;
				oInputs[aInputs[key].name]=sTextareaText;
				oDoms[aInputs[key].name]=aInputs[key];
				}
			}	
		var aInputs=oDom.getElementsByTagName("INPUT");
		for(var key in aInputs){
			if(!(!aInputs[key].getAttribute)){
				switch(aInputs[key].getAttribute("type")){
					case "radio":
						if(aInputs[key].checked==true){
							oInputs[aInputs[key].name]=aInputs[key].value;
							oDoms[aInputs[key].name]=aInputs[key];
							}
						break;
					case "text":
					case "datetime-local":
					case "datetime":
					case "time":
					case "date":
					case "textarea":
					case "tel":
					case "email":
					case "hidden":
						oInputs[aInputs[key].name]=aInputs[key].value;
						oDoms[aInputs[key].name]=aInputs[key];
						break;
					case "checkbox":
						if(!oDoubleChecked[aInputs[key].name+"to"+aInputs[key].value]){
							//console.log("checked по имени "+aInputs[key].name+" , со значением "+aInputs[key].value+"; checked="+aInputs[key].checked+"; НАйден");
							oDoubleChecked[aInputs[key].name+"to"+aInputs[key].value]=aInputs[key];
								if(aInputs[key].checked==true){
								if(!oInputs[aInputs[key].name]){
									oInputs[aInputs[key].name]=aInputs[key].value;
									oDoms[aInputs[key].name]=aInputs[key];
									}
								else{
									oInputs[aInputs[key].name]+=";"+aInputs[key].value;
									oDoms[aInputs[key].name]=aInputs[key];
									}
								} 
							}
						else{
							//console.log("checked по имени "+aInputs[key].name+" , со значением "+aInputs[key].value+"; checked="+aInputs[key].checked+"; Дублируется!!!");
							//console.log(oDoubleChecked);
							}
						break;
					}
				
				}
			}
		var oDoubleSelect=new Object;
		var aInputs=oDom.getElementsByTagName("SELECT");
		for(var key in aInputs){
			if(!(!aInputs[key].options)){
				if(!oDoubleSelect[aInputs[key].name+"to"+aInputs[key].options[aInputs[key].selectedIndex].value]){
					oInputs[aInputs[key].name]=aInputs[key].options[aInputs[key].selectedIndex].value;
					oDoms[aInputs[key].name]=aInputs[key];
					}
				}
			}
		
		oResult['array']=oInputs;
		oResult['object']=oDoms;
		for (var sName in oInputs){
			var sDelimetr=(oResult['string']=="")?"":"&";
			oResult['string']+=sDelimetr+sName+"="+oInputs[sName];
			}
		return oResult;
		}
	HTMLElement.prototype.getNextElement = function(oDom){
		var oDom=oDom||this;
		var oParent=oDom.parentNode;
		if(oParent.hasChildNodes()){
			for(var i in oParent.childNodes) {
				if(oParent.childNodes[i]==oDom){
					var oResult=oParent.childNodes[parseInt(i)+parseInt(2)];
					//console.log("!Нашли!");
					if(!oResult)
						return false;
					else	
						return oResult;
					}
				}
			}
		}
	HTMLElement.prototype.moveleft = function(iProcents,oDom){	
		var oDom=oDom||this;
		var iProcents=iProcents||100;
		if(oDom.style.display=='none'){
			oDom.style.display='block';
			MaxWidth(oDom,iProcents);
			}
		else{
			MinWidth(oDom,0);
			}
		}	
	HTMLElement.prototype.movetop = function(iProcents,oDom){	
		var oDom=oDom||this;
		var iProcents=iProcents||100;
		if(oDom.style.display=='none'){
			oDom.style.display='block';
			MaxHeight(oDom,iProcents);
			}
		else{
			MinHeight(oDom,0);
			}
		}	
	HTMLElement.prototype.getChildByClassName=function(sClass,oDom){
		var oDom=oDom||this;
		var aChilds=oDom.getElementsByClassName(sClass);
		for(var i=0;i<aChilds.length;i++){
			if(aChilds[i]){
				if(aChilds[i].getAttribute)
					return aChilds[i];
				}
			}
		}
	HTMLElement.prototype.getLastChild= function(oDom){
		var oDom=oDom||this;
		var iLenght=oDom.childNodes.length;
		var oResult=oDom.childNodes[iLenght-1]
		while(oResult!=null && oResult.nodeType == 3 && iLenght>1){ // skip TextNodes
			iLenght--;
			oResult = oDom.childNodes[iLenght-1]
			}
		return oResult;
		}
	HTMLElement.prototype.getFirstChild= function(oDom){
		var oDom=oDom||this;
		var firstChild = oDom.firstChild;
		while(firstChild != null && firstChild.nodeType == 3){ // skip TextNodes
			firstChild = firstChild.nextSibling;
			}
		return firstChild;
		}
	HTMLElement.prototype.getFirstChildByTag = function(sTag,oDom){
		var oDom=oDom||this;
		return GetChildByTag(sTag,oDom,true);
		}
	HTMLElement.prototype.getChildsByAttribute = function(sAttributeName,sAttributeValue,bRecurce,oDom){
		var bRecurce=bRecurce||false;
		var oDom=oDom||this;
		return GetChildByAttribute(sAttributeName,sAttributeValue,oDom,false,bRecurce);
		}	
	HTMLElement.prototype.getFirstChildByAttribute = function(sAttributeName,sAttributeValue,bRecurce,oDom){
		//console.log(sAttributeValue);
		var bRecurce=bRecurce||false;
		var oDom=oDom||this;
		return GetChildByAttribute(sAttributeName,sAttributeValue,oDom,true,bRecurce);
		}
	HTMLElement.prototype.getChildsByTag = function(sTag,oDom){
		var oDom=oDom||this;
		return GetChildByTag(sTag,oDom,false);
		}
	HTMLElement.prototype.getOnceChildById = function(id,oDom){
		var oDom=oDom||this;
		if(oDom.hasChildNodes()){
			for(var i = 0; i < oDom.childNodes.length; i++) {
				if(oDom.childNodes[i].id==id)
					return oDom.childNodes[i];
				}
			}
		return false;
		}
	HTMLElement.prototype.getSlowChildById = function(id,oDom){
		var oDom=oDom||this;
		var oNodes=oDom.getElementsByTagName('*');
		for(var key in oNodes){
			if(!(!oNodes[key].getAttribute)){
				if(oNodes[key].id==id)
					return oNodes[key];
				}
			}
		}
	HTMLElement.prototype.getChildById = function(id,oDom){
		var oDom=oDom||this;
		var oChild=RecurceFindById(oDom,id);
		return oChild;
		}
	HTMLElement.prototype.getParentByTag=function(sTag,oDom){ 
		var oDom=oDom||this;
		return GetMyParentByTag(sTag,oDom);
		}
	HTMLElement.prototype.WindowShow=function(oDom){ 
		var oDom=oDom||this;
		var oFanvcyMain=document.getElementById("fancyboxmain");
		var oFancyboxcontent=document.getElementById("fancyboxcontent");
		if((!(!oFanvcyMain)) && (!(!oFancyboxcontent))){
			oFancyboxcontent.innerHTML='';
			if(oDom.tagName=='IMG'){
				var oNewDom=document.createElement("IMG");
				oNewDom.src=oDom.src;
				//alert(oNewDom.src);
				oNewDom.onload = function() {
					var iCount=0;
					while((oNewDom.width<200 || oNewDom.height<200) && (iCount<10)){
						oNewDom.width=oNewDom.width*1.5;
						oNewDom.height=oNewDom.height*1.5;
						iCount++;
						}
					if(oNewDom.width>window.innerWidth){
						oNewDom.height=oNewDom.height*(window.innerWidth/oNewDom.width)-50;
						oNewDom.width=window.innerWidth-100;
						//alert(oNewDom.height);
						}
					if(oNewDom.height>=window.innerHeight){
						oNewDom.width=oNewDom.width*(window.innerHeight/oNewDom.height)-50;
						oNewDom.height=window.innerHeight-100;
						//alert(oNewDom.height);
						}
					var oFancywrap=document.getElementById("fancywrap");
					if(!(!oFancywrap)){
						oFancywrap.setAttribute("style","width:"+(parseInt(oNewDom.width)+100)+"px;height:"+(parseInt(oNewDom.height)+100)+";padding:50px;left:50%;top:50%;margin-left:-"+((parseInt(oNewDom.width)/2)+75)+"px;margin-top:-"+((parseInt(oNewDom.height)/2)+100)+"px;");
						//oFancywrap.style.width=oNewDom.width+20px;
						}
					oFancyboxcontent.appendChild(oNewDom);	
					oFanvcyMain.style.display="block";
					}
				}
			else{	
				//var oNewDom=oDom.cloneNode(true);*/
			oFancyboxcontent.appendChild(oDom);
			oFanvcyMain.style.display="block";
				}
			}
		}		
	

	HTMLElement.prototype.clicked=false;			

	HTMLElement.prototype.delete = function(oDom){
		var oDom=oDom||this;
		oDom.parentNode.removeChild(oDom);
		}
	HTMLElement.prototype.swtchdisplay = function(bOpacity,oDom){
		var oDom=oDom||this;
		var bOpacity=bOpacity||false;
		//.clicked);
		//alert(oDom.clicked);
		if(!(!oDom)){
			if(!bOpacity){
				var sDisplay;
				sDisplay=(oDom.style.display=='none')?"table-row":"none";
				oDom.style.display=sDisplay;
				//alert(sDisplay);
				}
			else{
				if(!oDom.clicked){//если первый раз нажали
					oDom.clicked=true;
					var now=oDom.style.opacity;
					//var shift=(now<0.5)?1:(-1);
					if(now<0.5){
						sDisplay="block";
						var shift=1;
						oDom.style.display="block";
						}
					else{
						sDisplay="none";
						var shift=(-1);
						}
					shift=(0.02*shift);	
					EditOpacity(oDom,shift,sDisplay);///Из за проблем на мобильном устростве, скрытй с помошью Opacity div мешает тачу, ввели параметр display=none 
					}
				}
			}
		}
	///////////////////////////////////////СТРОКОВЫЕ ФУНКЦИИ////////////////////////////////////////////////////////////////////////
	String.prototype.replaceAll = function(search, replace){
		return this.split(search).join(replace);
		}
	String.prototype.escape_string=function(){
		var sString=this;
		sString=sString.replace(/\//g,"%slaps%");
		sString=sString.replace(/\</g,"%open%");
		sString=sString.replace(/\>/g,"%close%");
		sString=sString.replace(/=/g,"%is%");
		sString=sString.replace(/&nbsp;/g,"%space%");
		sString=sString.replace(/ /g,"%space%");
		sString=sString.replace(/&quot;/g,"%quotes%");
		sString=sString.replace(/"/g,"%quotes%");
		sString=sString.replace(/\'/g,"%quotes%");
		sString=sString.replace(/\n/g,"%n%");
		sString=sString.replace(/\r/g,"%r%");
		sString=sString.replace(/\t/g,"%t%");
		sString=sString.replace(/-/g,"%tire%");
		sString=sString.replace(/\(/g,"%openbracket%");
		sString=sString.replace(/\)/g,"%closebracket%");
		sString=sString.replace(/&amp;/g,"%amp%");
		sString=sString.replace(/&/g,"");
		sString=sString.replace(/%/g,"**");
		return sString;
		}
	String.prototype.return_string=function(){
		var sString=this;
		sString=sString.replace(/\**/g,"%");
		sString=sString.replace(/%slaps%/g,'/');
		sString=sString.replace(/%open%/g,'<');
		sString=sString.replace(/%close%/g,'>');
		sString=sString.replace(/%is%/g,'=');
		sString=sString.replace(/%space%/g," ");
		sString=sString.replace(/%quotes%/g,'"');
		sString=sString.replace(/%n%/g,"\n");
		sString=sString.replace(/%r%/g,"\r");
		sString=sString.replace(/%t%/g,"\t");
		sString=sString.replace(/%tire%/g,"-");
		sString=sString.replace(/%amp%/g,"&amp;");
		sString=sString.replace(/%openbracket%/g,"(");
		sString=sString.replace(/%closebracket%/g,")");
		
		return sString;
		}
/////////////////////////////////////////////ФУНКЦИИ////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			  
function ClearTag(sText){	
	sText=(sText.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,'');
	return sText;
	}	
function ModalAjax(sPost,sPrefix,bReadGlobal,oParam){
	var oDom=document.createElement("DIV");
	var oParam=oParam||oAJAXTemplate['DrawSratic'];
	var bReadGlobal=bReadGlobal||false;
	var sPrefix=sPrefix||'';
	oDom.WindowShow();
	oDom.InsertAjax(sPost,sPrefix,bReadGlobal,oParam);
	}
function ShowWindow(sHtml,sHeader,sButton){
	var sButton=sButton||"";
	var sHeader=sHeader||"";
	var mess=document.getElementById("dialog");
	mess.style.display="block";
	var oHeader=document.getElementById("win_name");
	var oHtml=document.getElementById("inner");
	oHeader.innerHTML=sHeader;
	oHtml.innerHTML=sHtml;
	if(sButton!=''){
		var oButt=document.getElementById("oKButton");
		oButt.setAttribute("onclick",sButton);
		}
	}
	
	
function WaitAndSetSliderValue(oDom,e){
	if (e.keyIdentifier=="Enter")
		SetSliderValue(oDom);
	else	
		setTimeout(function() { SetSliderValue(oDom) }, 3000);
	}
function ListSlider(){
	var oSliderRight=document.getElementById("slider_right");
	if(!oSliderRight){
		setTimeout( function() {ListSlider() } , 5000);
		}
	else{
		oSliderRight.click();
		setTimeout( function() {ListSlider() } , 5000);
		}
	}
	
function MaxWidth(oDom,iProcents){
	var iProcents=iProcents||100;
	var iWidth=parseInt(oDom.style.width);
	if(iWidth<iProcents){
		iWidth++;
		oDom.style.width=iWidth+'%';
		setTimeout( function() {MaxWidth(oDom,iProcents) } , 2);
		}
	}
function MinWidth(oDom,iProcents){
	var iProcents=iProcents||0;
	var iWidth=parseInt(oDom.style.width);
	if(iWidth>0){
		iWidth--;
		oDom.style.width=iWidth+'%';
		setTimeout( function() {MinWidth(oDom,iProcents) } , 2);
		}
	else{
		oDom.style.display='none';
		}
	}	

	
function MaxHeight(oDom,iProcents){
	var iProcents=iProcents||100;
	var iHeight=parseInt(oDom.style.height);
	if(iHeight<iProcents){
		iHeight++;
		oDom.style.height=iHeight+'%';
		setTimeout( function() {MaxHeight(oDom,iProcents) } , 2);
		}
	}
function MinHeight(oDom,iProcents){
	var iProcents=iProcents||0;
	var iHeight=parseInt(oDom.style.height);
	if(iHeight>0){
		iHeight--;
		oDom.style.height=iHeight+'%';
		setTimeout( function() {MinHeight(oDom,iProcents) } , 2);
		}
	else{
		oDom.style.display='none';
		}
	}

function changeHash(sURL,sHref) {
	sHref=sHref||window.location.href;
	history.replaceState(null,null,sHref +  sURL);
	}	

function CreatePost(oDom){
	var oResult=oDom.ReadInputs();
	//console.log(oResult);
	return oResult['string'];
	}
		
function translite(str){
	var arr={' ':'_','а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ж':'g', 'з':'z', 'и':'i', 'й':'y', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o', 'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f','ь':'i','ъ':'i', 'ы':'i', 'э':'e', 'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ж':'G', 'З':'Z', 'И':'I', 'Й':'Y', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O', 'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Ы':'I', 'Э':'E', 'ё':'yo', 'х':'h', 'ц':'ts', 'ч':'ch', 'ш':'sh', 'щ':'shch', 'ъ':'i', 'ь':'i', 'ю':'yu', 'я':'ya', 'Ё':'YO', 'Х':'H', 'Ц':'TS', 'Ч':'CH', 'Ш':'SH', 'Щ':'SHCH', 'Ъ':'i', 'Ь':'i','Ю':'YU', 'Я':'YA'};
	var replacer=function(a){return arr[a]||a};
	//str=str.trim()
	while(str.indexOf(" ")+1)
		str=(str.replace(" ",'-'));
	return str.replace(/[А-яёЁ]/g,replacer)
	}


	
function GetMyParentByTag(sTag,oDom){
	if(!(!oDom)){
		if(oDom!=document.body){
			var sTagName=oDom.tagName;
			if((!(!sTagName)) && (sTagName!='')){
				if(sTagName.toLowerCase()==sTag.toLowerCase()){
					return oDom;
				}
			else{
					return GetMyParentByTag(sTag,oDom.parentNode);
				}
			}
		}
	else
		return false;
	
		}
	else
		return false;
	return false;
	}
	
function GetChildByAttribute(sAttributeName,sAttributeValue,oParent,bOnce,bRecurce){
	var bRecurce=bRecurce||false;
	if(oParent.hasChildNodes()){
		if(!bOnce){
			var aResult=new Array();
			}
		//console.log(sAttributeValue);
		for(var i = 0; i < oParent.childNodes.length; i++) {
			if(!(!oParent.childNodes[i].getAttribute)){
				if(!(!oParent.childNodes[i].getAttribute(sAttributeName))){
					if(oParent.childNodes[i].getAttribute(sAttributeName).toLowerCase()==sAttributeValue.toLowerCase()){
						if(bOnce){
							return oParent.childNodes[i];
							}
						else{
							aResult.push(oParent.childNodes[i]);
							}
						}
					}
				if(oParent.childNodes[i].hasChildNodes() && bRecurce){
					if(bOnce){
						return GetChildByAttribute(sAttributeName,sAttributeValue,oParent.childNodes[i],bOnce,bRecurce)
						}
					else{
						var oTemp=new Array();
						oTemp=GetChildByAttribute(sAttributeName,sAttributeValue,oParent.childNodes[i],bOnce,bRecurce);
						if(!(!oTemp)){
							aResult=aResult.concat(oTemp);
							//console.log(oTemp);
							}
						}
					}
				}
			}
		if((bOnce) || (aResult.length==0)){
			return false;
			}
		else{
			return aResult;
			}
		}
	else
		return false;
	}
	
function GetChildByTag(sTag,oParent,bOnce){
	if(oParent.hasChildNodes()){
		if(bOnce){
			var aResult=new Array();
			}
		for(var i = 0; i < oParent.childNodes.length; i++) {
			var sTagName=oParent.childNodes[i].tagName;
			if((!(!sTagName)) && (sTagName!='')){
				if(oParent.childNodes[i].tagName.toLowerCase()==sTag.toLowerCase()){
					if(bOnce){
						return oParent.childNodes[i];
						}
					else{
						aResult.push(oParent.childNodes[i]);
						}
					}
				}
			}
		if((bOnce) || (aResult.length==0)){
			return false;
			}
		else{
			return aResult;
			}
		}
	else
		return false;
	}
	
function RecurceFindById(oParent,id){
	if(oParent.hasChildNodes()){
		for(var i = 0; i < oParent.childNodes.length; i++) {
			if(oParent.childNodes[i].id==id){
				return oParent.childNodes[i];
				}
			if(oParent.childNodes[i].hasChildNodes()){
				var ChildChilds=RecurceFindById(oParent.childNodes[i],id);
				if(ChildChilds!=false){
					return ChildChilds;
					}
				}
			}
		}
	return 	false;
	}
function LoadScript(value){
	var oElement=document.createElement("script");
	oElement.defer = true;
	oElement.type = 'text/javascript';
	oElement.src=value;
	try{
		document.body.appendChild(oElement); 
		}
	catch(e){
		console.log(e);
		console.log(sText);
		}
	}
	

function WriteGlobal(sVarName,Value){
	aGlobal[sVarName]=Value;
	}	
	
function ReadGlobal(oVariable){
	var oVariable=oVariable||aGlobal;
	var sResult='';
	for(var key in oVariable){
		var sDelim=(sResult=='')?"":"&";
		if(oVariable[key]!='')
			sResult+=sDelim+key+"="+oVariable[key];
		}
	return sResult;
	}
		
/*function PhpToJsArray(sString,sTag){
	var oArray=sString.split(sTag);
	return oArray;
	}*/
	
	
	
function JSArrayToPhpString(oObject,sPairTag,sBracketsF,sBracketsL,sSemicolon){
	var sBracketsF=sBracketsF||"{";
	var sBracketsL=sBracketsL||"}";
	var sSemicolon=sSemicolon||";";
	var sPairTag=sPairTag||"=";
	var sResult='';
	for(var key in oObject){
		if(is_object(oObject[key])){
			//var sDel=(sResult=='')?"":";";
			var sDel=(sResult=='')?"":sSemicolon;
			key=(key=='')?"0":key;
			sResult+=sDel+key+sPairTag+sBracketsF+JSArrayToPhpString(oObject[key],sPairTag)+sBracketsL;
			//sResult+=sDel+key+"={"+JSArrayToPhpString(oObject[key],"=")+"}";
			}
		else{
			var sDel=(sResult=='')?"":sSemicolon;
			//var sDel=(sResult=='')?"":";";
			sResult+=sDel+key+sPairTag+oObject[key];
			//sResult+=sDel+key+"="+oObject[key];
			}
		}
	return sResult;
	}
		
function PhpToJsArray(sValue,sPairTag,sBracketsF,sBracketsL){
	var sBracketsF=sBracketsF||"{";
	var sBracketsL=sBracketsL||"}";
	var sPairTag=sPairTag||"=";
	var oObject=new Object();
	var aArray=sValue.split(";");
	for(var key in aArray){
		var aPair=aArray[key].split(sPairTag);
		if(aPair[1].indexOf(sBracketsF) + 1){
			var iFirst=aPair[1].indexOf(sBracketsF)+1;
			var iLast=aPair[1].lastIndexOf(sBracketsL);
			var sResultString=aPair[1].substring(iFirst,iLast);
			oObject[aPair[0]]=PhpToJsArray(sResultString);
			}
		else{
			oObject[aPair[0]]=aPair[1];
			}
		}
	return oObject;
	}

function PhpToJava(sString){//Множество записей в один большой массив
	//DOM1:div#dom2:img#dom3:head
	var oArray = new Array();
	var aRowsArray=sString.split("#");//Разделитель между строками
	for(var i = 0; i< aRowsArray.length; i++) {//бежим по строкамм
		var oAssocArray = new Object();
		if(aRowsArray[i]!=''){
			var ArraysArray=aRowsArray[i].split(";");//Разделитель между записями
			for(var q = 0; q < ArraysArray.length; q++) {
				var Elements=ArraysArray[q].split("="); //Разделитель значений
				oAssocArray[Elements[0]]=Elements[1];
				}
			oArray.push(oAssocArray);//записали полученный ассоциативный массив в строку
			}
		}
	return oArray;
	}

	
function isset(oObject){
	return (!(!oObject));
	}
	
function createRequestObject() {
		try { return new XMLHttpRequest() }
		catch(e) {
			try { return new ActiveXObject('Msxml2.XMLHTTP') }
			catch(e) {
				try { return new ActiveXObject('Microsoft.XMLHTTP') }
				catch(e) { return null; }
			}
		}
	}	


	
function CreateScript(sText){
		var oStart=sText.indexOf("function");
		//var iCount=(oStart+"function".length);
		//console.log(sText.substring(iCount));
		if(sText.substring(oStart+"function".length,1)!="'"){
			var oStop=sText.indexOf("(",oStart+"function".length);
			var sStr = sText.substring(oStart+"function".length,oStop);//остаток текста без скрипта
			sStr=sStr.trim();
			//console.log(sStr+"|");
			if(!(!oScripts[sStr])){
				oScripts[sStr].parentNode.removeChild(oScripts[sStr]);
				}
			var oElement=document.createElement("script");
			oElement.defer = true;
			oElement.type = 'text/javascript';
			//console.log(sText);
			oElement.text=sText;
			try{
				document.body.appendChild(oElement); 
				}
			catch(e){
				console.log(e);
				console.log(sText);
				}
			oScripts[sStr]=oElement;
			//console.log(">>"+sText);
		}
	}
	
function RecurseFindScript(sString){
	if(!(sString.indexOf('<script>') + 1)){
		return sString;
		//console.log('кончились скрипты');
		}
	//console.log("Обратились");
	var oStart_tag=sString.indexOf("<script");
	var oStart=sString.indexOf(">",oStart_tag);
	var oStop=sString.indexOf("<\/script>")+"<\/script>".length;
	//console.log(oStart);
	//console.log(oStop);
	var oLength=oStop-oStart;
	try{
		var sStr = sString.substring(0, oStart_tag)+sString.substring(oStop);//остаток текста без скрипта
		}
	catch(e){
		console.log(e);
		return false;
		}
	finally{
		var sJavaScript=sString.substring((oStart+">".length),(oStop-"<\/script>".length));//скрипт
		if(sStr.indexOf("<script>") + 1) {//если в теле все еще остался скрипт
			//console.log("В теле еще остался скрипт");
			var oNextSteep=new Object();
			oNextSteep=RecurseFindScript(sStr);//рекурсивно обрабатываем остаток
			//sStr+=oNextSteep['html'];
			sStr=oNextSteep['html'];
			sJavaScript+=oNextSteep['script'];
			}
		else{
			//console.log('кончились скрипты');
			}
		var oResult=new Object();
		oResult['script']=sJavaScript;
		oResult['html']=sStr;
		return oResult;
		}
	}
	
function RecurseAddScript(sString){
	if((sString.indexOf('function') + 1) && (sString.indexOf('function')!=sString.indexOf("function'"))){
		var oStart=sString.indexOf("function");
		var sBefore = sString.substring(0, oStart);//нашли что было до функции (переменные и прочие)
		if(sBefore!='')
			CreateScript(sBefore)
		if((sString.indexOf("function",oStart+"function".length)+1) && (sString.indexOf("function",oStart+"function".length)!=sString.indexOf("function'")) ){//если есть еще функции
			var oStop=sString.indexOf("function",oStart+"function".length);
			var sFunction=sString.substring((oStart),(oStop));//вытащили первую функцию
			CreateScript(sFunction);
			var SNextSteep =sString.substring(oStop);//остаток текста без вышеупомянутой функции
			RecurseAddScript(SNextSteep);
			}
		else{
			var sAfter=sString.substring(oStart);//колбаса текста, что после функции
			CreateScript(sAfter);
			}
		}
	else{
		CreateScript(sString);
		}
	}



function ShowModalWindow(){
	var oTemp=document.createElement("DIV");
	oTemp.WindowShow(); 
	return oTemp;
	}	
function AjaxWindow(path,params){
	ShowAjaxWindow(params,'',false);
	}
function ShowAjaxWindow(params,prefix,bReadGlobal){
	var oTemp=document.createElement("DIV");
	oTemp.WindowShow(); 
	oTemp.AddAjax(params,prefix,bReadGlobal,true,true);
	//ShowAjaxToDom(params,oTemp,prefix,bReadGlobal);
	}
function ShowGetWindow(url){
	var oTemp=document.createElement("DIV");
	oTemp.WindowShow(); 
	GetToDom(url,oTemp);	
	}

function AjaxWaitResult(sPost,sPrefix,bReadGlobal,TagClear){
	var sPrefix=sPrefix||'';
	var TagClear=TagClear||false;
	var bReadGlobal=bReadGlobal||false;
	sPost=(bReadGlobal)?sPost+"&"+ReadGlobal():sPost;
	//console.log(sPost);
	var http = createRequestObject();				
	if( http ) {
		http.open("POST", sPrefix+"ajax.php", false); 
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(sPost);
		var sResult=http.responseText;
		sResult=(TagClear)? (sResult.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sResult;
		//alert(sResult);
		return sResult;
		}
	}
	
function StringResultAjax(sPost,sPrefix,bShowTags){
	var sPrefix=sPrefix||'';
	var bShowTags=bShowTags||false;
	var http = createRequestObject();	
	if( http ) {
		http.open("POST", sPrefix+"ajax.php", false); 
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(sPost);
		var sResult=http.responseText;
		sResult=(!bShowTags)? (sResult.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sResult;
		//alert(sResult);
		return sResult;
		}
	}	
	

	
function AjaxWritePhpVar(sVarName,Value){
	var http = createRequestObject();	
	if( http ) {
			http.open("POST", "admin/ajax.php", true); 
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {//Call a function when the state changes.
			if(http.readyState == 4 && http.status == 200) {
				var result = http.responseText;
				result=(result.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,'');		
				}	
			}
			http.send("variable=write&var="+sVarName+"&value="+Value);
		}
	}
		
function AjaxReadPhpVar(sVarName){
	return StringResultAjax("variable=read&var="+sVarName,"admin/")	
	}
	
	
	
function ShowAjaxToDom(sPost,oDomElement,sPrefix,bReadGlobal,bValue){
	oDomElement.AddAjax(sPost,sPrefix,bReadGlobal,true,true,false);
	}	
function ShowAjaxToDomWithoutJS(sPost,oDomElement,sPrefix,bReadGlobal,bValue){
	oDomElement.AddAjax(sPost,sPrefix,true,true,true,false);
	}	
	
function GetToDom(link,oDomElement){
	var http = createRequestObject();					// создаем ajax-объект
	if( http ) {
		http.open('get', link);							// инициируем загрузку страницы
		http.onreadystatechange = function () {			// назначаем асинхронный обработчик события
			if(http.readyState == 4) {
				var responce = http.responseText;
				oDomElement.innerHTML=responce;
				}
			}		
		http.send(null); 
		}
	}	
		
function AjaxWindowButton(url,params,TagClear) {//Тестовая функция просто для отправки аякса и вывода ответа.. Можно использовать для вывода диалоговых окон
	//console.log("AjaxWindowButton");
	var sSize=sSize||"small"; 
	var http = createRequestObject();		
	if( http ) {
		http.open("POST", url, true); 
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		var TagClear=TagClear||false;
		http.onreadystatechange = function() {//Call a function when the state changes.
			if(http.readyState == 4 && http.status == 200) {
				var result = http.responseText;
				result=(TagClear==true) ? (result.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):result;
				//console.log(result);
				var arr = result.split("||||");
				arr[1]=arr[1].replace("</body>", "");
				arr[1]=arr[1].replace("</html>", "");

				ShowWindow(arr[0],'',arr[1]);
				//ShowWindow(result);
				//alert(result);
				return false;
				}	
			}
		http.send(params);
		}
	}
			
function addonadd() {
	var sPost="action=edit&addon=addons&procedure=showAddons&ajax=inwork";
	AjaxWindow("ajax.php",sPost,false);	
	}
	 
function AjaxResult(sParametrs,sPrefix,bReadGlobal,oParam){
	//console.log(sParametrs);
	var oParam=oParam||oAJAXTemplate['SendMysql'];
	var bReadGlobal=bReadGlobal||false;//по умолчанию - не читаем глобальные+
	var sPrefix=sPrefix||'';
	var bClearTags=oParam['bClearTags']||false; //не чистим теги
	var bAsynchronously=oParam['bAsynchronously']||false; //ждем ответа до выполнения следущих скрпитов
	if(bAsynchronously){
		sParametrs=(bReadGlobal)?sParametrs+"&"+ReadGlobal():sParametrs;
		console.log(sParametrs);
		var http = createRequestObject();				
		if( http ) {
			var sURL=sPrefix+"ajax.php";
			//console.log(sURL);
			http.open("POST", sURL, true); 
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {//Call a function when the state changes.
				if(http.readyState == 4 && http.status == 200) {
					var result = http.responseText;
					result=(bClearTags==true) ? (result.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):result;
					//console.log(result);
					return result;
					}	
				}
			http.send(sParametrs);
			}
		}
	else{
		var sResult=WaitFromAjax(sParametrs,sPrefix,bReadGlobal);
		sResult=(bClearTags)? (sResult.replace(/<\/?[^>]+>/gi, '')).replace(/(^\s+|\s+$)/g,''):sResult;
		return sResult;
		}
	}
function WaitFromAjax(sParametrs,sPrefix,bReadGlobal){//синхронный ответ от аякса
	var sPrefix=sPrefix||'';
	var bReadGlobal=bReadGlobal||false;
	sParametrs=(bReadGlobal)?sParametrs+"&"+ReadGlobal():sParametrs;
	var http = createRequestObject();				
	if( http ) {
		http.open("POST", sPrefix+"ajax.php", false); 
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(sParametrs);
		var sResult=http.responseText;
		return sResult;
		}
	}
function SwitchSize(oMasterDom,oSlaveDom){
	if(oMasterDom.style.width!='50%'){
		oMasterDom.style.width='50%';
		oSlaveDom.style.width='50%';
		oSlaveDom.style.display='table-row';
		}
	else{
		oMasterDom.style.width='100%';
		oSlaveDom.style.width='0%';
		oSlaveDom.style.display='none';
		}
	}
				

	
function SendPost(sPost){
	sPost=sPost||'';
	if(sPost!=''){
		var oForm=document.createElement("form");
		oForm.setAttribute("method","POST");
		sPost=sPost.replace('index.php?', '');
		var aRows=sPost.split('&');
		 for (var i = 0; i < aRows.length; i++) {
			var aCell=aRows[i].split('=');
			var sKey=aCell[0];
			var sValue=aCell[1];
			var oInput=document.createElement("input");
			oInput.setAttribute('type','hidden');
			oInput.setAttribute('name',sKey);
			oInput.setAttribute('value',sValue);
			oForm.appendChild(oInput);
			}
		document.body.appendChild(oForm);	
		oForm.submit();	
		}
	}
	
	function ReadChildNodes(oDomElement){
			var ChildArray=new Array();
			if(oDomElement.hasChildNodes()){
				for(var i = 0; i < oDomElement.childNodes.length; i++) {
					ChildArray.push(oDomElement.childNodes[i]);
					if(oDomElement.childNodes[i].hasChildNodes()){
						var ChildChildsArray=ReadChildNodes(oDomElement.childNodes[i]);
						for(var q = 0; q < ChildChildsArray.length; q++) {
							ChildArray.push(ChildChildsArray[q]);
							}
						}
					}
				}
			return 	ChildArray;
			}

function GeneratePost(oDiv){
			var sResult='send=post&';
			var oAllChildren=ReadChildNodes(oDiv);
			for(var q = 0; q < oAllChildren.length; q++) {
				if(	(oAllChildren[q].nodeName=='INPUT') ||
					(oAllChildren[q].nodeName=='SELECT') ||
					(oAllChildren[q].nodeName=='TEXTAREA') ||
					(oAllChildren[q].nodeName=='BUTTON')){
						if(oAllChildren[q].name!='' ){
							sResult=sResult+'&'+oAllChildren[q].name+'='+oAllChildren[q].value;
							//alert(oAllChildren[q].nodeName+";"+oAllChildren[q].name+";"+oAllChildren[q].value);
						}
					}
				}
			return sResult;
			}	
ListSlider();


function CreatFancybox(){
	var oFancy=document.createElement("div");
	oFancy.setAttribute("class","fancybox-overlay fancybox-overlay-fixed");
	oFancy.id='fancyboxmain';
	oFancy.style.display="none";
	//console.log('Создали фанси');
	oFancy.innerHTML='<div class="fancybox-wrap fancybox-desktop fancybox-type-inline fancybox-opened" tabindex="-1" style="" id="fancywrap"><div class="fancybox-skin" style=""><div class="fancybox-outer"><div class="fancybox-inner" style=""><div class="popup-callback" id="popup-callback" style="display: block;"><div class="popup-inner"><div class="popup-header"><div class="popup-title"></div></div><div class="popup-content" style="margin-left: 0;"><div class="order-form form-container"><div class="little-title lt-center" id="fancyboxcontent">Контент</div><div class="success" style="display: none;"> <div class="text"></div> </div><div class="loader"></div></div></div></div></div></div></div><a title="Close" class="fancybox-item fancybox-close" onclick="document.getElementById(\'fancyboxmain\').style.display=\'none\'; return false;"></a></div></div>';
	document.body.appendChild(oFancy);
	
	//console.log(oFancy);
	}
//////////////////СПЕЦИАЛЬНЫЕ СКРИПТЫ ДЛЯ АДМИНКИ/////


////////////////////////////////////////////////////
////////////////////Эвенты редактора////////////////
var bResize=false;
var oResize;
var iStartX=false;
var iStartY=false;

function ModuleToolsPanel(oDom){
	//console.log("ModuleToolsPanel");
	var sAddonId=oDom.getAttribute('data-idaddon');
	var sUsers=oDom.getAttribute('data-array');
	//sUsers=sUsers.escape_string(); 
	alert(sUsers);
	console.log(sUsers);
	var sPost="action=edit&addon=addons&procedure=showAddonsSetting&ajax=inwork&id="+sAddonId+"&DomID="+oDom.id+"&idwindow=modal_window&users="+sUsers;
	ShowAjaxWindow(sPost)
	}

function EditorOnClick(event){
	var oDom=event.target;
	if(!(!oDom.getAttribute)){
		if((!(!oDom.getAttribute("data-name"))) && (oDom.getAttribute("data-name")=="addon")){
			
			if(!(!parent.ModuleToolsPanel)){
				//console.log("есть родитель");
				var oPanelFunction=parent.ModuleToolsPanel;
				}
			else{
				//console.log("нет родителя");
				var oPanelFunction=ModuleToolsPanel;	
				}
			
			//alert(oPanelFunction);
			oPanelFunction(oDom);
			}
		}
	
	}
function EditorOnMouseDown(event){
	/*var oDom=event.target;
	if(!(!oDom.getAttribute)){
		switch(oDom.tagName){
			case "IMG":
			case "TD":
				iStartX=event.clientX;
				iStartY=event.clientY;
				bResize=true;
				oResize=oDom;
				console.log(oDom);
				break;
			}
		}*/
	}
function EditorOnMouseDblclick(event){
	var oDom=event.target;
	if(!(!oDom.getAttribute)){
		switch(oDom.tagName){
			case "IMG":
			case "TD":
				iStartX=event.clientX;
				iStartY=event.clientY;
				bResize=true;
				oResize=oDom;
				//console.log(oDom);
				break;
			}
		}
	}
function CheckFunction(sValue){ 
	return typeof window[sValue];
	}
function EditorOnMouseMove(event){
	if(!(!oResize) && bResize){
		var iWidth=oResize.clientWidth;
		var iHeight=oResize.clientHeight;
		var NewWidth=0;
		var NewHeight=0;
		if(!(!iStartX)){
			//console.log(oResize);
			//console.log('Ширина='+iWidth);	
			NewWidth=parseInt(parseInt(iWidth)+parseInt(event.clientX - iStartX));
			iStartX=event.clientX;
			//console.log('clientX='+event.clientX);
			//console.log('iStartX='+iStartX);
			//console.log('Разность='+parseInt(event.clientX - iStartX));
			//console.log('NewWidth='+NewWidth);
			oResize.width=NewWidth;
			//console.log('oResize.width='+oResize.clientWidth);
			}
		if(!(!iStartY)){
			NewHeight=parseInt(parseInt(iHeight)+parseInt(event.clientY -iStartY));
			iStartY=event.clientY;
			oResize.Height =NewHeight;
			}
		}
	}
function EditorOnMouseUP(event){
	bResize=false;
	iStartX=false;
	iStartY=false;
	//console.log(event.target);
	}
function SendGetAjaxAdmin(params,oDomElement){
	//console.log(params);
	//console.log(oDomElement);
	params="upload=ajax&"+params;
	var http = createRequestObject();				
	if( http ) {
		http.open("GET","index.php?"+params, false); 
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(params);
		var sResult=http.responseText;
		//console.log(sResult);
		oDomElement.innerHTML=sResult;
		//return sResult;
		}
	}	
////////////////////////////////////////////////////	

function ScrollTo(sDom,sDom1){
	var oDom=document.getElementById(sDom);
	var oDom1=document.getElementById(sDom1);
	if(!(!oDom1)){
		var oParent=oDom.parentNode;
		if(oParent.hasChildNodes()){
			var iHeight=0;
			for(var i in oParent.childNodes) {
				if(!(!oParent.childNodes[i].getAttribute)){
					if(oParent.childNodes[i]==oDom1.parentNode){
						break;
						}
					else{
						iHeight+=parseInt(oParent.childNodes[i].offsetHeight);
						//console.log('oneHeight='+oParent.childNodes[i].offsetHeight);
						//console.log(oParent.childNodes[i]);
						}
					}
				}
			var iBodyHeight=document.body.scrollHeight-document.body.clientHeight;
			var iNowScroll=document.body.scrollTop;
			var iDom=oDom1.offsetHeight;
			var iCheck=parseInt(iHeight-(iDom+(iDom)));
			/*console.log('________________________');
			console.log('iBodyHeight='+iBodyHeight);
			
			console.log('iNowScroll='+iNowScroll);
			console.log('iDom='+iDom);
			console.log('iCheck='+iCheck);
			console.log('iHeight='+iHeight);*/
			if(iNowScroll>iCheck){
				if(!(!aGlobal['onScroll']) && aGlobal['onScroll']!=''){
					if(typeof window[aGlobal['onScroll']] == 'function'){
						window[aGlobal['onScroll']]();
						//console.log(aGlobal['onScroll']);
						}
					}
				}
			}
		}
	else{
	//это кусок универсальный
		if(!(!oDom)){
			var iDomHeight=oDom.offsetHeight;
			var iBodyHeight=document.body.scrollHeight-document.body.clientHeight;
			var iNowScroll=document.body.scrollTop;
			if(iBodyHeight-iNowScroll<iDomHeight){
				if(!(!aGlobal['onScroll']) && aGlobal['onScroll']!=''){
					if(typeof window[aGlobal['onScroll']] == 'function'){
						window[aGlobal['onScroll']]();
						}
					}
				}
			}
		}
	}
	
function onPrintStart(){
	var oHiddenDiv=document.getElementById("hiddendiv");
	if(oHiddenDiv){
		oHiddenDiv.style.display="block";
		}
	if(oHead){
		oHead.style.display="none";
		}
	}
function showMenu(oDom){
	if(oDom){
		var sAtr=oDom.getAttribute("data-name");
		if((!sAtr)||(sAtr=="еще")){
			oDom.setAttribute("data-name","скрыть");
			var sDisplay="display:block !important";
			}
		else{
			oDom.setAttribute("data-name","еще");
			var sDisplay="display:block";
			}
		var oUl=oDom.parentNode;
		var oLi=oUl.getElementsByTagName("LI");
		for(var i=0;i<oLi.length;i++){
			if(oLi[i]){
				if(oLi[i].getAttribute){
					oLi[i].setAttribute("style",sDisplay);
					}
				}
			}
		}
	}
/*//Эвент на загрузку страницы*/
function init() {
	if (arguments.callee.done) return;
	arguments.callee.done = true;
	if (_timer) clearInterval(_timer);
	CreatFancybox();
	if(!bAdmin)
		wheight = document.body.clientHeight;
	var oEcho=document.getElementsByClassName("echo");
	if(oEcho){
		for(var i=0;i<oEcho.length;i++){
			oEcho[i].setAttribute("onClick","showMenu(this)");
			}
		}
	var oEcho=document.getElementsByClassName("logo");
	if(oEcho){
		for(var i=0;i<oEcho.length;i++){
			oEcho[i].setAttribute("onClick","window.location=''");
			}
		}
	if(typeof window['SetEvents'] == 'function'){
		if(!(!SetEvents)){
			if(!bAdmin)
				SetEvents();
			} 
		}
	if(typeof window['OnDocumentLoadEvent'] == 'function'){
		if(!(!OnDocumentLoadEvent)){
			if(!bAdmin)
				OnDocumentLoadEvent();
			} 
		}
	if(aGlobal['wait']){
		for(var key in aGlobal['wait']){
			//console.log(aGlobal['wait'][key]);
			if(typeof window[aGlobal['wait'][key]] == 'function'){
				window[aGlobal['wait'][key]]();
				}
			}
		}
	}

/* for Mozilla/Opera9 */
if (document.addEventListener) {
  document.addEventListener("DOMContentLoaded", init, false);
}

/* for Internet Explorer */
/*@cc_on @*/
/*@if (@_win32)
  document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
  var script = document.getElementById("__ie_onload");
  script.onreadystatechange = function() {
    if (this.readyState == "complete") {
      init(); // call the onload handler
    }
  };
/*@end @*/

/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) { // sniff
  var _timer = setInterval(function() {
    if (/loaded|complete/.test(document.readyState)) {
      init(); // call the onload handler
    }
  }, 10);
}

/* for other browsers */
window.onload = init;