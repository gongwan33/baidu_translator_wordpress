/*
*  Name: Bai Du translator - baidu-translate.js 
*  Version: V1.0
*  Contributor: Wagner
*  Company: JoyBin
*/

var g_sDefaultLanguage = 'zh';
var g_sCurg_sCurLanguageLanguage = 'zh';
var g_oTargetEle = document.body;
var g_bTransingFlag = false;
var g_bReverseTransedFlag = false;
var g_iCountClick = 0;
var g_aDomTextList = null;
var g_aParamsFromServer;
var g_bTransFromAuto = false;
var g_aTransedArrayRes = null;
var g_iTimeOutCount = 0;
var g_bTransePromiseRes = true;
var g_iTranseProcess = 0;

function fnInitDropList() {
    var oSelector = document.getElementById('baidu_translate_drop_list');
    var oItem = oSelector.getElementsByTagName('ul');
    var oItemLi = oItem[0].getElementsByTagName('li');
    var oTitle = document.getElementById('drop_title');

    for(var i = 0; i < oItem.length; i++) {
        oItem[i].style.display = 'none';
    }

    //init drop title
    var oBoxText = oTitle.getElementsByTagName('span');
    var oCurLangLi = document.getElementById(g_sCurLanguage);
    oBoxText[0].innerHTML = oCurLangLi.innerHTML;
    
    var iMaxLen = 0;
    for(var i = 0; i < oItemLi.length; i++) {
        var iLen = $(oItem).width();
        if (iLen > iMaxLen) {
            iMaxLen = iLen;
        }

        oItemLi[i].onclick = function(ev) {
            oBoxText[0].innerHTML = this.innerHTML;
            var sLang = this.id;

            
            if (sLang === g_sDefaultLanguage && g_sCurLanguage !== sLang) {
                g_bReverseTransedFlag = true;
            } else {
                g_bReverseTransedFlag = false;
            }
            g_bTransingFlag = true;

            fnTransDomContent(g_oTargetEle, sLang);
        }
    };

    $(oSelector).width(iMaxLen + 30); //30 is for array space.

    oTitle.onclick = function(ev) {
        if (g_bTransingFlag == false) {
            for(var i = 0; i < oItem.length; i++) {
                if(g_iCountClick%2 == 0) {
                    oItem[i].style.display = 'block';
                }
                else {
                    oItem[i].style.display = 'none';
                }
            }
            g_iCountClick++;
        }
        ev.cancelBubble = true;
    };

    document.onclick = function(ev) {
       for(var i = 0; i < oItem.length; i++) {
           oItem[i].style.display = 'none';
       }
       g_iCountClick = 0;

       ev.cancelBubble = true;
    };
 
}

function fnRequestParamsFromServer() {
    $.ajaxSettings.async = false;

    var oCurScript = document.getElementById('baidu-translate');
    var sAjaxUrl = oCurScript.src.match(/(\S*)\/\S*?\/\S*?\/\S*?\/\S*?\/\S*?\/\S*?/)[1] + '/wp-admin/' + 'admin-ajax.php';
    var oPromiseParams = new Promise(function (resolve, reject) {
        $.ajax({
            url: sAjaxUrl,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'option-params'
            },
            error: function(oXMLHttpRequest, sTextStatus, oErrorThrown) {
                reject(sTextStatus);
            },
            success: function (oRes) {
                resolve(oRes);
            } 
        });
    });

    return oPromiseParams;
}

function fnTraverseDomTree(oEleIn, fnCallback) {
    var oChildNodes = oEleIn.childNodes;
    var xRes = null;
    var aBypassIdList = g_aParamsFromServer.bypassid_list;

    for (var i = 0; i < oChildNodes.length; i++) {
        var sTagName = oChildNodes[i].tagName;
        var sId = oChildNodes[i].id;
        var bSkipSign = false;
        
        if(sTagName === 'SCRIPT' || sTagName === 'STYLE') {
            continue;
        }

        if(oChildNodes[i].className === 'notranslate') {
            continue;
        }
        
        for (var j = 0; j < aBypassIdList.length; j++) {	
            if (aBypassIdList[j] === sId) {
                bSkipSign = true;
                break;
            }
        }

        if (bSkipSign === true) {
            continue;
        }

        if(oChildNodes[i].childNodes.length == 0) {
            type = oChildNodes[i].nodeType;
            if(type === 3) {
                if(typeof fnCallback === "function") {
                    xRes = fnCallback(oChildNodes[i]);
                }
            }
        } else {
            fnTraverseDomTree(oChildNodes[i], fnCallback);
        }
    }

    return xRes;
}

function fnTranslateTextList(sLang, aTextList) {
	const UNITLENGTH = 2048;
	var sOriginText = '';
	var aPromises = new Array();
	
    for (var i = 0; i < aTextList.length; i++) {
        var sText = aTextList[i].origin;
        if (sText.length < UNITLENGTH) {
            if (sOriginText.length + sText.length > UNITLENGTH) {
                var oPromise = fnDoTranslate(sLang, sOriginText);
                aPromises.push({promise:oPromise, partFlag:false});
                sOriginText = '';
            }
            sOriginText += sText + '\n';
        } else {
            if (sOriginText !== '') {
                var oPromise = fnDoTranslate(sLang, sOriginText);
                aPromises.push({promise:oPromise, partFlag:false});
                sOriginText = '';
            }

            var iPointer = 0;
            while ( iPointer < sText.length) {
                var sPartString = sText.substr(iPointer, UNITLENGTH);
                var oPromise = fnDoTranslate(sLang, sPartString);

                aPromises.push({promise:oPromise, partFlag:true});
                iPointer += UNITLENGTH;
            }
        }
    }

    if (sOriginText !== '') {
        var oPromise = fnDoTranslate(sLang, sOriginText);
        aPromises.push({promise:oPromise, partFlag:false});
        sOriginText = '';
    }

    return aPromises;
}

function fnTidyTextList(aTextList, aTransedResArray) {
    if(aTextList.length !== aTransedResArray.length) {
        console.log("Translate Error! textList len:" + aTextList.length + "transedArray len:" + transedResArray.length);
        //console.log(aTextList);
        //console.log(aTransedResArray);
        return null;
    }
    for( var i = 0; i < aTextList.length; i++) {
        if(aTextList[i].index === i) {
            aTextList[i].origin = aTransedResArray[i].src;
            aTextList[i].transed = aTransedResArray[i].dst;
        } else {
            console.log("ERROR: index doesn't match!");
        }
    }

    return aTextList;
}

function fnTransDomContent(oEleIn, sLang) {
    if(g_aDomTextList === null) {
        console.log("ERROR: Have not init dom text list.");
        g_bTransingFlag = false;
        return;
    }

    if(g_bReverseTransedFlag === true || (g_sCurLanguage !== g_sDefaultLanguage && g_sCurLanguage !== sLang)) {
        var iDomIndex = 0;
        fnTraverseDomTree(oEleIn, function (oChildNode) {
            var sTrimedOrigin = oChildNode.textContent.replace(/(^\s*)|(\s*$)/, '');
            if(sTrimedOrigin != '') {
                //console.log(g_aDomTextList[iDomIndex]);
                //console.log(oChildNode.textContent);
                for(var i = iDomIndex; i < g_aDomTextList.length; i++) {
                    if(sTrimedOrigin.indexOf(g_aDomTextList[i].transed) != -1) {
                        oChildNode.textContent = oChildNode.textContent.replace(sTrimedOrigin, g_aDomTextList[i].origin);    
                        iDomIndex++;
						break;
                    }
				}
            }
            return 0;
        });
    }

    var aPromiseTranslates = fnTranslateTextList(sLang, g_aDomTextList);
    
	g_aTransedArrayRes = null;
	g_aTransedArrayRes = new Array();
	g_bTransePromiseRes = true;
	g_iTranseProcess = 0;
	
    for (var i = 0; i < aPromiseTranslates.length; i++) {
		var oPromise = aPromiseTranslates[i].promise;
        if( oPromise === null) {
            console.log("Warning: Do not need translate.");
            g_sCurLanguage = sLang;
            g_bTransingFlag = false;
            return;
        }

        (function (i) {
            oPromise.then(
                    function(aTransedArray) {
                        g_aTransedArrayRes.push({index:i, partFlag: aPromiseTranslates[i].partFlag, data:aTransedArray});
                        g_iTranseProcess++;
                    }
                    ).catch(
                        function(reason) {
                            console.log(reason);
                            g_bTransePromiseRes = false;
                        }
                        );
        })(i);
	}
	
	
	var iCheckRes = setInterval( function () {
        g_iTimeOutCount++;
        if(g_iTimeOutCount > 30) {
            clearInterval(iCheckRes);
            g_bTransingFlag = false;
            g_sCurLanguage = sLang;
            return;
        }

		if(g_bTransePromiseRes === false) {
			clearInterval(iCheckRes);
	        g_bTransingFlag = false;
	        g_sCurLanguage = sLang;
			return;
		}
		
		if (g_iTranseProcess < aPromiseTranslates.length) {
			return;
		}

        g_aTransedArrayRes.sort(function (a, b) {
            return a.index - b.index;
        });

        var aTransedData = new Array();
        //console.log(g_aTransedArrayRes);

        var aTempData = {src:'', dst:''};
        for(var i = 0; i < g_aTransedArrayRes.length; i++) {
            if( g_aTransedArrayRes[i].partFlag === false ) {
                if (aTempData.src !== '') {
                    aTransedData.push(aTempData);
                    aTempData = {src:'', dst:''};
                }
                aTransedData = aTransedData.concat(g_aTransedArrayRes[i].data);
            } else {
                aTempData.src += g_aTransedArrayRes[i].data[0].src;
                aTempData.dst += g_aTransedArrayRes[i].data[0].dst;
            }
        }

        if (aTempData.src !== '') {
            aTransedData = aTransedData.concat(aTempData);
            aTempData = {src:'', dst:''};
        }


        //console.log(transedData);
        var aRes = fnTidyTextList(g_aDomTextList, aTransedData);
        //console.log(g_aDomTextList);

        if ( aRes === null ) {
            g_sCurLanguage = sLang;
            g_bTransingFlag = false;
            clearInterval(iCheckRes);
            return;
        }

        var iDomIndex = 0;
        fnTraverseDomTree(oEleIn, function (oChildNode) {
            var sTrimedOrigin = oChildNode.textContent.replace(/(^\s*)|(\s*$)/, '');
            if( sTrimedOrigin != '' ) {
                //console.log(oChildNode.textContent);
		        //console.log(g_aDomTextList[iDomIndex]);
		        for(var i = iDomIndex; i < g_aDomTextList.length; i++) {
                    if(sTrimedOrigin.indexOf(g_aDomTextList[i].origin) !== -1) {
				        //console.log('ok!' + sTrimedOrigin);
                        oChildNode.textContent = oChildNode.textContent.replace(sTrimedOrigin, g_aDomTextList[i].transed);    
                        iDomIndex++;
				        break;
                    }
	            }
            }
            return 0;
        });

        g_bTransingFlag = false;
        g_sCurLanguage = sLang;
        clearInterval(iCheckRes);
    }, 800);

    return;
}

function fnDoTranslate(sLang, sText) {
   if (g_sCurLanguage === sLang) {
       //console.log("Don't need to translate.");
       return null;
   }

   if (sLang == g_sDefaultLanguage) {
       //console.log("Don't need to translate or need to reverse.");
       return null;
   }

   var sQuery = sText;
   var sFrom = g_bTransFromAuto?'auto':g_sDefaultLanguage;
   var sTo = sLang;
   //console.log(sFrom);
   //console.log(sTo);
   //console.log(sText);

   $.ajaxSettings.async = false;
   //Test key for every developer. May be unavaliable sometime.
   //var sAppid = '2015063000000001';
   //var sKey = '12345678';

   //The developer key provided by JoyBin for everyone. 2,000,000 words/month.
   var sAppid = '20161227000034807';
   var sKey = 'qkCZxszPG_kFz9Ed3AvQ';
   var sSalt = (new Date).getTime();
   var sStr1 = sAppid + sQuery + sSalt + sKey;
   var sSign = MD5(sStr1);

   if ( g_aParamsFromServer.appid !== 'joybin' && g_aParamsFromServer.key !== 'joybin') {
       sAppid = g_aParamsFromServer.appid;
       sKey = g_aParamsFromServer.key;
   }

   var oPromiseTranslate = new Promise(function (resolve, reject) {
       $.ajax({
           url: 'http://api.fanyi.baidu.com/api/trans/vip/translate',
           type: 'post',
           dataType: 'jsonp',
           data: {
               q: sQuery,
               appid: sAppid,
               salt: sSalt,
               from: sFrom,
               to: sTo,
               sign: sSign
           },
           error: function(oXMLHttpRequest, sTextStatus, oErrorThrown) {
               reject(sTextStatus);
           },
           success: function (oRes) {
               resolve(oRes.trans_result);
           } 
       });
   });

   return oPromiseTranslate;
}

window.onload = function() {
	var oPromiseParamsFromServer = fnRequestParamsFromServer();
    oPromiseParamsFromServer.then(
            function (oParamsFromServer) {
                g_sDefaultLanguage = oParamsFromServer.default_lang;
                g_sCurLanguage = oParamsFromServer.default_lang;

                switch (oParamsFromServer.translate_range) {
                    case 'body':
                        g_oTargetEle = document.body;
                        break;
                    
                    case 'head':
                        g_oTargetEle = document.head;
                        break;
                     
                    case 'all':
                        g_oTargetEle = document;
                        break;
                }

                g_bTransFromAuto = oParamsFromServer.auto_lang;

                //console.log(oParamsFromServer);
                fnInitDropList();

                g_aParamsFromServer = oParamsFromServer;
                var aTextList = new Array();
                var iTranseIndex = 0;
                fnTraverseDomTree(g_oTargetEle, function (oChildNode) {
                    var sTrimedOrigin = oChildNode.textContent.replace(/(^\s*)|(\s*$)/, '');
					sTrimedOrigin = sTrimedOrigin.replace(/\n/g, '');
                    if(sTrimedOrigin != '') {
                        //console.log(oChildNodes[i].tagName);
                        aTextList.push({origin: sTrimedOrigin, transed: '', index: iTranseIndex});
                        iTranseIndex++;
                    }

                    return aTextList;
                });
                g_aDomTextList = aTextList;
            }
            ).catch(
                function (sReason) {
                    console.log("ERROR: " + sReason);
                }
                );

}
