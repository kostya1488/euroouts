				var iReferenceScroll = new Object();
				var sAlbomCount = 100;
				iReferenceScroll['albom'] = 0; //��������� ������, ������ ��� ����������� ����������� ���������
				iReferenceScroll['photo'] = 0; //��������� ������, ������ ��� ����������� ����������� ���������
				var oOffsets = new Object();

				function ReadOffsets(oDom) {
				    if (!(!oDom)) {
				        if (oDom.hasChildNodes()) {
				            for (var i = 0; i < oDom.childNodes.length; i++) {
				                if (!(!oDom.childNodes[i].getAttribute)) {
				                    if (!(!oDom.childNodes[i].getAttribute("data-number"))) {
				                        if (oDom.childNodes[i].id == 'start') {
				                            oOffsets[oDom.childNodes[i].offsetTop] = oDom.childNodes[i].getAttribute("data-number");
				                        }
				                    }
				                }
				            }
				        }
				    }
				}

				function PoginationShow(oParent) {
				    var oParent = oParent || document.body;
				    if (!(!oParent.getAttribute)) {
				        if (!(!oParent.getAttribute("data-prefix"))) {
				            var sPrefix = oParent.getAttribute("data-prefix");
				        }
				        if (!(!oParent.getAttribute("data-count"))) {
				            var iAlbomCount = Math.floor(parseInt(parseInt(oParent.getAttribute("data-count")) / 40));
				        }
				    } else
				        var iAlbomCount = Math.floor(parseInt(parseInt(sAlbomCount) / 40));
				    //console.log(oParent.getElementsByTagName('*'));
				    var oDom = oParent.getSlowChildById("pogination");
				    if (!(!oDom)) {
				        var iWidth = oDom.offsetWidth;
				        var fShift = Math.floor(iWidth / 20);
				        var iCount = parseInt(fShift);
				        //console.log(iWidth);
				        oDom.innerHTML = '';

				        var oSpan = document.createElement("span");
				        oSpan.setAttribute("data-prefix", sPrefix);
				        oSpan.setAttribute("data-operation", "first");
				        oSpan.setAttribute("data-max", iAlbomCount);
				        oSpan.setAttribute("onClick", "onSelectNext(this)");
				        oSpan.innerHTML = "<<";
				        oDom.appendChild(oSpan);
				        var oSpan = document.createElement("span");
				        oSpan.setAttribute("data-prefix", sPrefix);
				        oSpan.setAttribute("data-operation", "prev");
				        oSpan.setAttribute("data-max", iAlbomCount);
				        oSpan.setAttribute("onClick", "onSelectNext(this)");
				        oSpan.innerHTML = "<";
				        oDom.appendChild(oSpan);
				        var iAnother = 4;
				        if (!(!aGlobal[sPrefix + 'Page'])) {
				            var iStart = parseInt(aGlobal[sPrefix + 'Page']) + 1;
				            var iSelect = iStart;
				            var iMiddle = Math.floor((iCount - iAnother) / 2);
				            if (iStart > iMiddle) {
				                iStart = iStart - iMiddle;
				            } else {
				                iStart = 1;
				            }
				            var iStop = iCount + iStart - iAnother;
				        } else {
				            var iStart = 1;
				            var iStop = iCount - iAnother;
				            var iSelect = 1;
				        }
				        iStop = (iStop <= (iAlbomCount + 2)) ? iStop : iAlbomCount + 2;
				        for (var i = iStart; i < iStop; i++) {
				            var oSpan = document.createElement("span");
				            oSpan.innerHTML = i;
				            if (iSelect == i) {
				                //alert(iSelect+"="+i);
				                oSpan.setAttribute("class", "active");
				            }
				            oSpan.setAttribute("data-prefix", sPrefix);
				            oSpan.setAttribute("data-operation", i);
				            oSpan.setAttribute("data-max", iAlbomCount);
				            oSpan.setAttribute("onClick", "onSelectNext(this)");
				            oDom.appendChild(oSpan);
				        }
				        var oSpan = document.createElement("span");
				        oSpan.innerHTML = ">";
				        oSpan.setAttribute("data-prefix", sPrefix);
				        oSpan.setAttribute("data-operation", "next");
				        oSpan.setAttribute("data-max", iAlbomCount);
				        oSpan.setAttribute("onClick", "onSelectNext(this)");
				        oDom.appendChild(oSpan);
				        var oSpan = document.createElement("span");
				        oSpan.setAttribute("data-prefix", sPrefix);
				        oSpan.setAttribute("data-operation", "last");
				        oSpan.setAttribute("data-max", iAlbomCount);
				        oSpan.setAttribute("onClick", "onSelectNext(this)");
				        oSpan.innerHTML = ">>";
				        oDom.appendChild(oSpan);
				        ReadOffsets(document.getElementById(sPrefix + "UL"));
				        //console.log(oOffsets);
				    }
				}

				function onSelectNext(oDom) {
				    if (!(!oDom)) {
				        if (!(!oDom.getAttribute)) {
				            var sPrefix = oDom.getAttribute("data-prefix");
				            var sOperation = oDom.getAttribute("data-operation");
				            var iMax = parseInt(oDom.getAttribute("data-max"));
				            //console.log(sPrefix+'Page');
				            if (((!(!aGlobal[sPrefix + 'Page'])) || aGlobal[sPrefix + 'Page'] == 0) && (oDom.getAttribute("class") != 'active')) {
				                var iNow = parseInt(aGlobal[sPrefix + 'Page']);
				                switch (sOperation) {
				                    case "next":
				                        if (iNow < iMax)
				                            iNow++;
				                        break;
				                    case "prev":
				                        if (iNow > 0)
				                            iNow--;
				                        break;
				                    case "last":
				                        iNow = iMax;
				                        break;
				                    case "first":
				                        iNow = 1;
				                        break;
				                    default:
				                        iNow = parseInt(sOperation) - 1;
				                        //console.log(iNow);
				                        break;
				                }
				                aGlobal[sPrefix + 'Page'] = iNow;
				                var oDom = document.getElementById(sPrefix + "UL");
				                //alert(aGlobal[sPrefix+'Page']);
				                oDom.parentNode.scrollTop = 0;
				                oDom.WriteFromAjax("action=edit&addon=albomsalpha&procedure=Admin_" + sPrefix + "List", '', true, true, true, false);
				                PoginationShow(oDom.parentNode.parentNode);
				            }
				        }
				    }
				}

				function OnEndScrol(oDom, event) {
				    if (!(!oDom)) {
				        var sPrefix = "albom";
				        if (!(!oDom.getAttribute)) {
				            if (!(!oDom.getAttribute("data-what"))) {
				                sPrefix = oDom.getAttribute("data-what");
				            }
				        }
				        console.log(oDom);
				        console.log(sPrefix);
				        var iScrollTop = oDom.scrollHeight;
				        var iNowScroll = oDom.scrollTop;
				        var iScrollDirection = iNowScroll - iReferenceScroll['albom'];
				        var iClientHeight = oDom.clientHeight;
				        var iLeft = parseInt(iScrollTop);
				        var iRight = parseInt(parseInt(iNowScroll) + parseInt(iClientHeight) + Number(10));
				        var bRedraw = true;
				        if (iLeft <= iRight) {
				            if (!(!aGlobal[sPrefix + 'Page']))
				                var iPage = parseInt(parseInt(aGlobal[sPrefix + 'Page']) + Number(1));
				            else
				                iPage = 1;
				            aGlobal[sPrefix + 'Page'] = iPage;
				            var oDom = document.getElementById(sPrefix + "UL");
				            var oLast = oDom.getLastChild();
				            if (!(!oLast)) {
				                if (!(!oLast.getAttribute)) {
				                    if (!(!oLast.getAttribute("data-number"))) {
				                        aGlobal[sPrefix + 'Page'] = parseInt(parseInt(oLast.getAttribute("data-number")) + Number(1));
				                    }
				                }
				            }
				            oDom.WriteFromAjax("action=edit&addon=albomsalpha&procedure=Admin_" + sPrefix + "List", '', true, false, true, false);
				            PoginationShow(oDom.parentNode.parentNode);
				            bRedraw = false;
				        } else {
				            if (iNowScroll == 0) {
				                var oDom = document.getElementById(sPrefix + "UL");
				                var oFirst = oDom.getFirstChild();
				                var iNew = 0;
				                var bSatisfied = false;
				                if (!(!oFirst)) {
				                    if (!(!oFirst.getAttribute)) {
				                        if (!(!oFirst.getAttribute("data-number"))) {
				                            if (oFirst.getAttribute("data-number") == '0') {
				                                bSatisfied = true;
				                                aGlobal[sPrefix + 'Page'] = 0;
				                                PoginationShow(oDom.parentNode.parentNode);
				                            } else {
				                                iNew = parseInt(oFirst.getAttribute("data-number")) - 1;
				                            }
				                        }
				                    }
				                }
				                if (!bSatisfied) {
				                    aGlobal[sPrefix + 'Page'] = iNew;
				                    oDom.WriteFromAjax("action=edit&addon=albomsalpha&procedure=Admin_" + sPrefix + "List", '', true, false, true, false, true);
				                    bRedraw = false;
				                    PoginationShow(oDom.parentNode.parentNode);
				                    oDom.parentNode.scrollTop = 10;
				                    iReferenceScroll['albom'] = 10;
				                }
				            }
				        }
				        if (bRedraw) {
				            var iDirection = iScrollDirection / Math.abs(iScrollDirection);
				            var iShiftScroll = Math.abs(iScrollDirection);
				            var iTemp = 0;
				            if (iNowScroll > (iShiftScroll * 2)) {
				                for (var iQ = 0; iQ < iShiftScroll; iQ++) {
				                    var iIndexScroll = parseInt(parseInt(iNowScroll) + parseInt(iQ));
				                    if (!(!oOffsets[iIndexScroll])) {
				                        iTemp = iQ;
				                        var iNowPage = oOffsets[iIndexScroll];
				                        var iPlus = (iDirection > 0) ? 0 : -1;
				                        iNowPage = (iNowPage > 0) ? parseInt(parseInt(iNowPage) + parseInt(iPlus)) : iNowPage;
				                        aGlobal[sPrefix + 'Page'] = iNowPage;
				                        PoginationShow(oDom.parentNode);
				                        break;
				                    }
				                }
				            } else {

				            }
				        }
				        iReferenceScroll['albom'] = iNowScroll;
				    }
				}

				function ClosePhotos() {
				    var oPhoto = document.getElementById("photolist");
				    var oAlboms = document.getElementById("albomlist");
				    oAlboms.style.width = "100%";
				    oPhoto.style.display = "none";
				    oPhoto.WriteFromAjax("action=edit&addon=albomsalpha&procedure=Admin_HTML_photoList", '', true, true, true, false);
				    PoginationShow(oAlboms);
				    var oButton = document.getElementById("show");
				    if (!(!oButton)) {
				        oButton.style.display = "none";
				    }
				}

				function ShowPhotos(oDom) {
				    if (!(!oDom)) {
				        if (!(!oDom.getAttribute)) {
				            //console.log(oDom.offsetTop);
				            if (!(!oDom.getAttribute("data-albom"))) {
				                var iAlbom = oDom.getAttribute("data-albom");
				                aGlobal["iAlbom"] = iAlbom;
				                var oPhoto = document.getElementById("photolist");
				                var oAlboms = document.getElementById("albomlist");
				                oAlboms.style.width = "260px";
				                oPhoto.style.display = "block";
				                oPhoto.WriteFromAjax("action=edit&addon=albomsalpha&procedure=Admin_HTML_photoList", '', true, true, true, false);
				                PoginationShow(oAlboms);
				                var oButton = document.getElementById("show");
				                if (!(!oButton)) {
				                    oButton.style.display = "block";
				                    oButton.setAttribute("onclick", "ClosePhotos()");
				                }
				            }
				        }
				    }
				}