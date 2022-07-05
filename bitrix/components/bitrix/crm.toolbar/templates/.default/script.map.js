{"version":3,"sources":["script.js"],"names":["exports","main_core","main_core_events","ui_buttons","crm_router","main_popup","namespace","Reflection","instance","ToolbarEvents","babelHelpers","classCallCheck","this","defineProperty","ToolbarComponent","_EventEmitter","inherits","_this","possibleConstructorReturn","getPrototypeOf","call","setEventNamespace","Event","ready","bindEvents","bind","assertThisInitialized","createClass","key","value","_this2","buttonNode","document","querySelector","entityTypeId","Number","dataset","button","ButtonManager","createFromNode","subscribeCategoriesUpdatedEvent","reloadCategoriesMenu","categoryId","emitTypeUpdatedEvent","data","emit","TYPE_UPDATED","emitCategoriesUpdatedEvent","CATEGORIES_UPDATED","subscribeTypeUpdatedEvent","callback","subscribe","menu","getMenuWindow","ajax","runAction","then","response","startKey","items","categories","menuItems","forEach","item","id","indexOf","push","options","destroy","unbindAll","getContainer","category","link","Router","Instance","getItemListUrlInCurrentView","splice","text","Text","encode","name","href","toString","setText","params","menuWindow","Menu","show","catch","console","log","errors","get","window","top","getClass","BX","Crm","EventEmitter","UI","Main"],"mappings":"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","file":"script.map.js"}