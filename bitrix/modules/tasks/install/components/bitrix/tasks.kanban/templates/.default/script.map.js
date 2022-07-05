{"version":3,"sources":["script.js"],"names":["BX","namespace","Tasks","KanbanComponent","ClickSort","event","item","order","params","hasClass","layout","menuItems","menuWindow","i","c","length","removeClass","addClass","ajax","method","dataType","url","ajaxHandlerPath","data","action","sessid","bitrix_sessid","ajaxParams","onsuccess","onCustomEvent","this","filterId","defaultPresetId","onReady","bind","delegate","tooltip","PopupWindow","closeByEsc","angle","offsetLeft","darkMode","autoHide","zIndex","content","message","show","addCustomEvent","roleId","filterManager","Main","getById","alert","fields","preset_id","additional","ROLEID","filterApi","getApi","setFilter","ROLE_TYPE","window","history","pushState","getData","counter","filter","toggleByField","PROBLEM","filterValue","reg","str","getSlider","getUrl","test","confirm","denyAction","TourGuideController","options","tours","guide","initGuides","prototype","firstTimelineTaskCreation","expiredTasksDeadlineChange","viewMode","FirstTimelineTaskCreationTourGuide","backgroundCheck","ExpiredTasksDeadlineChangeTourGuide","getGuide","tour","popupData","ajaxActionPrefix","start","eventCreate","target","lastItem","setLastItem","Kanban","setPopupParamFunction","setPopupParam","node","popUp","getPopup","targetWidth","offsetWidth","setMinWidth","setMaxWidth","setAngle","offset","getPopupContainer","style","left","pos","UI","Tour","Guide","steps","document","querySelectorAll","title","text","article","events","onShow","getGridContainer","classList","add","getCurrentStep","getTarget","getContainer","setTarget","showNextStep","removeCustomEvent","onClose","remove","animateAha","getColumn","columns","getColumns","unsetAnimateAha","offAhaMode","close","getType","onAhaMode","prevColumn","setTimeout","runAction","onEvents","markShowedStep","step","analyticsLabel","getCurrentStepIndex","currentStepIndex","userId","counterToCheck","itemId","calendarPopup","isStopped","isPullListening","bindEvents","eventHandlers","user_counter","onUserCounter","command","apply","stop","proxy","onExpiredCounterKanbanReloaded","Number","newCounter","view_role_originator","expired","view_role_responsible","then","result","querySelector","selector","getRenderToContainer","closest","push","Step","onDeadlineChangeClick","eventData","isCorrectItem","getIsStopped","setCalendarPopup","calendar","popup","onCalendarPopupClose","onDeadlineChanged","currentTime","Date","value","getTime","onItemRemoved","getItem","buttons"],"mappings":"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","file":"script.map.js"}