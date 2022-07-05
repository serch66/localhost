{"version":3,"sources":["dialog.bundle.js"],"names":["this","BX","Messenger","exports","im_application_core","im_provider_rest","promise","pull_client","ui_vue","im_lib_logger","im_lib_utils","im_component_recent","im_component_dialog","im_component_textarea","pull_component_status","im_const","im_mixin","main_core_events","BitrixVue","component","props","userId","default","initialDialogId","mixins","DialogCore","DialogReadMessages","DialogQuoteMessage","DialogClickOnCommand","DialogClickOnMention","DialogClickOnUserName","DialogClickOnMessageMenu","DialogClickOnMessageRetry","DialogClickOnUploadCancel","DialogClickOnReadList","DialogSetMessageReaction","DialogOpenMessageReactionList","DialogClickOnKeyboardButton","DialogClickOnChatTeaser","DialogClickOnDialog","TextareaCore","TextareaUploadFile","data","dialogId","created","EventEmitter","subscribe","onOpenMessenger","beforeDestroy","unsubscribe","computed","DeviceType","isDialog","Utils","dialog","isChatId","isEnableGesture","isEnableGestureQuoteFromRight","methods","_ref","event","id","logEvent","name","_len","arguments","length","params","Array","_key","Logger","info","apply","concat","template","DialogApplication","_this","undefined","babelHelpers","classCallCheck","inited","initPromise","Promise","rootNode","node","document","createElement","VueVendorV2","initCore","then","initComponent","initComplete","createClass","key","value","_this2","resolve","reject","Core","ready","controller","_this3","console","log","getStore","commit","getDialogId","options","quoteEnable","autoplayVideo","darkBackground","addRestAnswerHandler","DialogRestHandler","create","store","context","getters","application","chatId","diskFolderId","createVue","el","getUserId","vue","promise$$1","getLocalize","parseInt","toString","getHost","location","origin","getSiteId","addLocalize","phrases","Application","Provider","Rest","Lib","window","Const","Mixin","Event"],"mappings":"AAAAA,KAAKC,GAAKD,KAAKC,OACfD,KAAKC,GAAGC,UAAYF,KAAKC,GAAGC,eAC3B,SAAUC,EAAQC,EAAoBC,EAAiBC,EAAQC,EAAYC,EAAOC,EAAcC,EAAaC,EAAoBC,EAAoBC,EAAsBC,EAAsBC,EAASC,EAASC,GAChN,aAUAT,EAAOU,UAAUC,UAAU,4BACzBC,OACEC,QACEC,QAAS,GAEXC,iBACED,QAAS,MAGbE,QAASR,EAASS,WAAYT,EAASU,mBAAoBV,EAASW,mBAAoBX,EAASY,qBAAsBZ,EAASa,qBAAsBb,EAASc,sBAAuBd,EAASe,yBAA0Bf,EAASgB,0BAA2BhB,EAASiB,0BAA2BjB,EAASkB,sBAAuBlB,EAASmB,yBAA0BnB,EAASoB,8BAA+BpB,EAASqB,4BAA6BrB,EAASsB,wBAAyBtB,EAASuB,oBAAqBvB,EAASwB,aAAcxB,EAASyB,oBAClhBC,KAAM,SAASA,IACb,OACEC,SAAU,IAGdC,QAAS,SAASA,IAChB5C,KAAK2C,SAAW3C,KAAKuB,gBACrBN,EAAiB4B,aAAaC,UAAU,gBAAiB9C,KAAK+C,kBAEhEC,cAAe,SAASA,IACtB/B,EAAiB4B,aAAaI,YAAY,gBAAiBjD,KAAK+C,kBAElEG,UACEC,WAAY,SAASA,IACnB,OAAOpC,EAASoC,YAElBC,SAAU,SAASA,IACjB,OAAO1C,EAAa2C,MAAMC,OAAOC,SAASvD,KAAK2C,WAEjDa,gBAAiB,SAASA,IACxB,OAAO,OAETC,8BAA+B,SAASA,IACtC,OAAOzD,KAAKwD,iBAAmB,OAGnCE,SACEX,gBAAiB,SAASA,EAAgBY,GACxC,IAAIC,EAAQD,EAAKjB,KACjB1C,KAAK2C,SAAWiB,EAAMC,IAExBC,SAAU,SAASA,EAASC,GAC1B,IAAK,IAAIC,EAAOC,UAAUC,OAAQC,EAAS,IAAIC,MAAMJ,EAAO,EAAIA,EAAO,EAAI,GAAIK,EAAO,EAAGA,EAAOL,EAAMK,IAAQ,CAC5GF,EAAOE,EAAO,GAAKJ,UAAUI,GAG/B5D,EAAc6D,OAAOC,KAAKC,MAAM/D,EAAc6D,QAASP,GAAMU,OAAON,MAIxEO,SAAU,q8BAWZ,IAAIC,EAAiC,WAEnC,SAASA,IACP,IAAIC,EAAQ5E,KAEZ,IAAImE,EAASF,UAAUC,OAAS,GAAKD,UAAU,KAAOY,UAAYZ,UAAU,MAC5Ea,aAAaC,eAAe/E,KAAM2E,GAClC3E,KAAKgF,OAAS,MACdhF,KAAKiF,YAAc,IAAIhF,GAAGiF,QAC1BlF,KAAKmE,OAASA,EACdnE,KAAK0E,SAAW,KAChB1E,KAAKmF,SAAWnF,KAAKmE,OAAOiB,MAAQC,SAASC,cAAc,OAC3DtF,KAAK4D,MAAQ,IAAIpD,EAAO+E,YACxBvF,KAAKwF,WAAWC,KAAK,WACnB,OAAOb,EAAMc,kBACZD,KAAK,WACN,OAAOb,EAAMe,iBAIjBb,aAAac,YAAYjB,IACvBkB,IAAK,WACLC,MAAO,SAASN,IACd,IAAIO,EAAS/F,KAEb,OAAO,IAAIkF,QAAQ,SAAUc,EAASC,GACpC7F,EAAoB8F,KAAKC,QAAQV,KAAK,SAAUW,GAC9CL,EAAOK,WAAaA,EACpBJ,WAKNH,IAAK,gBACLC,MAAO,SAASJ,IACd,IAAIW,EAASrG,KAEbsG,QAAQC,IAAI,oBACZvG,KAAKoG,WAAWI,WAAWC,OAAO,mBAChCnD,QACEX,SAAU3C,KAAK0G,eAEjBC,SACEC,YAAa,KACbC,cAAe,KACfC,eAAgB,SAGpB9G,KAAKoG,WAAWW,qBAAqB1G,EAAiB2G,kBAAkBC,QACtEC,MAAOlH,KAAKoG,WAAWI,WACvBJ,WAAYpG,KAAKoG,WACjBe,QAASnH,QAEX,IAAIsD,EAAStD,KAAKoG,WAAWI,WAAWY,QAAQ,iBAAiBpH,KAAKoG,WAAWiB,YAAYX,eAE7F,GAAIpD,EAAQ,CACVtD,KAAKoG,WAAWI,WAAWC,OAAO,mBAChCnD,QACEgE,OAAQhE,EAAOgE,OACfC,aAAcjE,EAAOiE,cAAgB,KAK3C,OAAOvH,KAAKoG,WAAWoB,UAAUxH,MAC/ByH,GAAIzH,KAAKmF,SACTzC,KAAM,SAASA,IACb,OACErB,OAAQgF,EAAOqB,YACf/E,SAAU0D,EAAOK,gBAIrBhC,SAAU,6EACTe,KAAK,SAAUkC,GAChBtB,EAAO3B,SAAWiD,EAClB,OAAO,IAAIzC,QAAQ,SAAUc,EAASC,GACpC,OAAOD,WAKbH,IAAK,eACLC,MAAO,SAASH,IACd3F,KAAKgF,OAAS,KACdhF,KAAKiF,YAAYe,QAAQhG,SAG3B6F,IAAK,QACLC,MAAO,SAASK,IACd,GAAInG,KAAKgF,OAAQ,CACf,IAAI4C,EAAa,IAAI3H,GAAGiF,QACxB0C,EAAW5B,QAAQhG,MACnB,OAAO4H,EAGT,OAAO5H,KAAKiF,eAOdY,IAAK,YACLC,MAAO,SAAS4B,IACd,IAAIrG,EAASrB,KAAKmE,OAAO9C,QAAUrB,KAAK6H,YAAY,WACpD,OAAOxG,EAASyG,SAASzG,GAAU,KAGrCwE,IAAK,cACLC,MAAO,SAASY,IACd,OAAO1G,KAAKmE,OAAOxB,SAAW3C,KAAKmE,OAAOxB,SAASoF,WAAa,OAGlElC,IAAK,UACLC,MAAO,SAASkC,IACd,OAAOC,SAASC,QAAU,MAG5BrC,IAAK,YACLC,MAAO,SAASqC,IACd,MAAO,QAOTtC,IAAK,cACLC,MAAO,SAASsC,EAAYC,GAC1B,OAAOrI,KAAKoG,WAAWgC,YAAYC,MAGrCxC,IAAK,cACLC,MAAO,SAAS+B,EAAY9D,GAC1B,OAAO/D,KAAKoG,WAAWyB,YAAY9D,OAKvC,OAAOY,EA5I4B,GA+IrCxE,EAAQwE,kBAAoBA,GAvNhC,CAyNG3E,KAAKC,GAAGC,UAAUoI,YAActI,KAAKC,GAAGC,UAAUoI,gBAAmBrI,GAAGC,UAAUoI,YAAYrI,GAAGC,UAAUqI,SAASC,KAAKvI,GAAGA,GAAGA,GAAGA,GAAGC,UAAUuI,IAAIxI,GAAGC,UAAUuI,IAAIxI,GAAGC,UAAUD,GAAGC,UAAUwI,OAAOA,OAAOzI,GAAGC,UAAUyI,MAAM1I,GAAGC,UAAU0I,MAAM3I,GAAG4I","file":"dialog.bundle.map.js"}