{"version":3,"sources":["util.js"],"names":["BX","namespace","mergeEx","Tasks","Util","formatTimeAmount","time","format","parseInt","isNaN","sign","Math","abs","hours","floor","minutes","seconds","nPad","num","substring","length","result","delay","action","actionCancel","ctx","DoNothing","this","timer","f","args","arguments","setTimeout","apply","cancel","clearTimeout","showByClass","node","hasClass","removeClass","hideByClass","addClass","fadeToggleByClass","duration","onComplete","animateShowHide","toShow","opacity","toHide","complete","fadeSlideToggleByClass","height","getInvisibleSize","fadeSlideHToggleByClass","width","params","type","isElementNode","p","Promise","reject","invisible","way","resolve","animate","start","finish","style","cssText","isFunction","call","step","state","rt","Runtime","animations","anim","k","easing","transition","transitions","linear","splice","fulfill","push","stop","pos","isEnter","e","getKeyFromEvent","isEsc","window","event","keyCode","which","filterFocusBlur","cbFocus","cbBlur","timeout","focus","eventArgs","bind","bindInstantChange","cb","value","debounce","toString","disable","setAttribute","enable","removeAttribute","getMessagePlural","n","msgId","pluralForm","langId","message","isArray","fireGlobalTaskEvent","taskData","options","taskDataUgly","allowedActions","indexOf","toUpperCase","task","taskUgly","onCustomEvent","top","sliders","SidePanel","Instance","getOpenSliders","forEach","slider","frameWindow","getFrameWindow","hintManager","bindHelp","scope","target","className","bindDelegate","passCtx","onHelpShow","onHelpHide","showDisposable","body","id","parameters","isPlainObject","closeLabel","autoHide","show","callback","util","hashCode","random","hintPopup","popup","wasDisposed","content","isNotEmptyString","title","create","attrs","text","htmlspecialchars","replace","html","margin","children","props","href","events","click","hide","PopupWindowManager","closeByEsc","closeIcon","angle","offsetLeft","offsetTop","close","userOptions","save","disableSeveral","pack","enabled","data","innerHTML","PopupWindow","lightShadow","darkMode","bindOptions","position","zIndex","onPopupClose","destroy","helpWindow","setAngle","offset","MouseTracker","coords","x","y","document","delegate","pageX","clientX","documentElement","scrollLeft","clientLeft","pageY","clientY","scrollTop","clientTop","getCoordinates","clone","getInstance","mouseTracker"],"mappings":"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","file":"util.map.js"}