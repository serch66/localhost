{"version":3,"sources":["manager.js"],"names":["BX","namespace","Crm","EntityEditorDupManager","this","_id","_settings","_groupInfos","_isEnabled","_serviceUrl","_entityTypeName","_form","_controller","prototype","initialize","id","settings","type","isNotEmptyString","util","getRandomString","prop","getBoolean","getObject","getString","get","CrmDupController","create","serviceUrl","entityTypeName","form","searcSummaryPosition","isEnabled","search","initialSearch","getGroupInfo","groupId","hasOwnProperty","getGroup","ensureGroupRegistered","group","registerGroup","registerField","config","field","unregisterField","self","EntityBizprocManager","_moduleId","_entity","_documentType","_autoExecuteType","_containerId","_fieldName","_validParameters","_formInput","_editor","_starter","_hasParameters","getInteger","_contentNode","Bizproc","Starter","moduleId","entity","documentType","onBeforeSave","result","promise","Promise","deferredWaiter","window","setTimeout","delegate","fulfill","getStatus","showAutoStartParametersPopup","contentNode","callback","onFillParameters","bind","e","console","log","onAfterSave","data","parameters","getFormElement","props","name","appendChild","value","messages","items","EntityRestPlacementManager","getSetting","bottomButton","proxy","openMarketplace","defer","initializeInterface","rest","Marketplace","open","PLACEMENT","AppLayout","PlacementInterface","initializePlacement","entityTypeId","_entityTypeId","entityId","_entityId","resizeWindow","params","cb","f","layoutName","height","parseInt","style","p","pos","width","reloadData","EntityEvent","fireUpdate"],"mappings":"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","file":"manager.map.js"}