{"version":3, "file":"template_fad64f827e4a8775a02722a048c3e2d5.js", "sections": [{"offset": { "line": 2, "column": 0 }, "map": {"version":3,"sources":["/bitrix/templates/bitrix24/bitrix24.js"],"names":["iframeMode","window","top","search","location","sliderMode","indexOf","href","BX","addCustomEvent","response","encodeURIComponent","B24","getBackUrl","status","redirectUrl","uniquePopupId","bindElement","params","lightShadow","className","offsetTop","offsetLeft","angle","offset","config","JCClock","setOptions","centerXInline","centerX","centerYInline","centerY","minuteLength","hourLength","popupHeight","inaccuracy","cancelCheckClick","command","message","counters","clone","updateCounters","delegate","UI","Notification","Center","notify","content","this","iDecrement","decrementCounter","counter","updateInformer","im-message","ready","style","visibility","type","isDomNode","querySelector","timeman","header","webkitTransition","width","offsetWidth","document","body","opacity","setTimeout","addClass","bind","removeClass","removeAttribute","error","code","connectionStatus","sendErrorCode","browser","SupportLocalStorage","key","substring","value","getClass","placementInterface","rest","AppLayout","initializePlacement","prototype","showHelper","cb","query","isNumber","isNotEmptyString","isPlainObject","param","length","Helper","show","throttle","onScroll","b24ConnectionStatusState","b24ConnectionStatus","b24ConnectionStatusText","b24ConnectionStatusTimeout","formateDate","time","util","str_pad","getHours","getMinutes","openLanguagePopup","button","PopupWindowManager","create","closeIcon","autoHide","closeByEsc","changeLanguage","lang","backUrl","pathname","getQueryString","ignoredParams","vars","split","isArray","result","i","pair","equal","in_array","informer","innerHTML","send","Bitrix24","LeftMenuClass","node","showNotifyPopup","hasClass","BXIM","closeNotify","openNotify","showMessagePopup","toggleMessenger","closeBanner","bannerId","userOptions","save","banner","minHeight","overflow","border","easing","duration","start","height","offsetHeight","finish","transition","makeEaseOut","transitions","quart","step","state","marginBottom","complete","display","animate","showLoading","timeout","loader","isReady","windowScroll","GetWindowScrollPos","getAttribute","scrollTop","setAttribute","goUp","fn","scroll","scrollTo","onCustomEvent","isFunction","toggleMenu","menuItem","messageShow","messageHide","menuBlock","findChild","parentNode","tagName","menuItems","findChildren","toggleText","nextSibling","animation","scrollHeight","id","opening","maxHeight","linear","cssText","licenseInfoPopup","popupId","title","showDemoButton","LicenseInfoPopup","showPartnerForm","arParams","Bitrix24PartnerForm","bInit","popup","zIndex","overlay","draggable","restrict","titleBar","right","buttons","PopupWindowButtonLink","text","events","click","popupWindow","close","onAfterPopupShow","setContent","ajax","post","site_id","Timemanager","inited","layout","block","timer","info","event","tasks","data","clock","formatTime","ts","bSec","parseInt","formatWorkTime","h","m","s","formatCurrentTime","hours","minutes","seconds","mt","isAmPmMode","init","reportJson","proxy","onDataRecieved","onPlannerDataRecieved","onPlannerQueryResult","onTaskTimerChange","registerFormat","statusBlock","taskTime","taskTimer","BXTIMEMAN","ShowFormWeekly","onTimemanClick","setBindOptions","mode","popupOptions","position","onPopupClose","redraw","Open","action","taskTimerSwitch","TIMER","RUN_TIME","TASK","TIME_SPENT_IN_LOGS","TIME_ESTIMATE","setTimer","setFrom","Date","INFO","DATE_START","dt","TIME_LEAKS","from","stopTimer","stop","redraw_planner","TASKS_ENABLED","TASKS_COUNT","CALENDAR_ENABLED","EVENT_TIME","PLANNER","STATE","CAN_OPEN","getStatusName","container","statusClass","startAnimation","endAnimation","OPEN_NOW","ob","animationTimeout","blinkAnimation","blinkLimit","blinkTimeout","startBlink","setInterval","endBlink","clearInterval","toggle","Bitrix24InviteDialog","Init","contentColor","contentNoPaddings","loadForm","InviteDialog","onInviteDialogClose","ShowForm","adjustPosition","ReInvite","reinvite_user_id","reinvite","sessid","bitrix_sessid","b24ConnectionStatusStateText","clearTimeout","connectionPopup","isFloat","attrs","data-role","data-float","children","props","html","IsMac","reload","nextNode","insertBefore","showPartnerOrderForm","Math","min","documentElement","clientHeight","sec","onPopupFirstShow","w","d","u","createElement","async","src","now","getElementsByTagName","upgradeButtonRedirect","url","COUNTER_URL","licensePath","LICENSE_PATH","host","HOST","PopupBlur","PopupWindow","apply","arguments","setBlurBg","__proto__","constructor","getPopupContainer","backgroundImage","getComputedStyle","backgroundColor","classList","add","styles","createTextNode","appendChild","head","setBlurBgAngle","anglyStyle","anglyStyles","setPadding","padding","Type","getContentContainer","removeProperty"],"mappings":"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","file":"bitrix24.map.js"}}]}