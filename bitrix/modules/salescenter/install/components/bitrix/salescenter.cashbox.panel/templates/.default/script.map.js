{"version":3,"sources":["script.js"],"names":["BX","namespace","SaleCenterCashbox","init","config","this","cashboxParams","cashbox","TileGrid","Grid","draw","options","Item","apply","arguments","title","image","itemSelected","itemSelectedColor","itemSelectedImage","layout","container","clipTitle","company","controls","buttonAction","price","cashboxId","id","data","prototype","__proto__","constructor","getContent","wrapper","create","props","className","children","getImage","getTitle","getStatus","getLabel","events","click","onClick","bind","setSelected","style","backgroundImage","recommendation","itemLabel","text","message","addClass","backgroundColor","appendChild","setUnselected","removeClass","querySelector","parentNode","removeChild","type","sliderOptions","allowChangeHistory","width","onCloseComplete","e","reloadCashboxItem","handler","showMenu","SidePanel","Instance","open","connectPath","showItemMenu","item","menu","menuItemIndex","itemNode","menuitemId","util","getRandomString","menuItems","hasOwnProperty","DELIMITER","push","delimiter","NAME","link","LINK","onclick","tile","moreTabsMenu","close","PopupMenu","autoHide","offsetLeft","offsetTop","closeByEsc","onPopupClose","popupWindow","destroy","onPopupDestroy","show","self","ajax","runComponentAction","mode","then","response"],"mappings":"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","file":"script.map.js"}