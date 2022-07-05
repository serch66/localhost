<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
      integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>


<style>
 #map {
        width: 600px; height: 600px; padding: 10px; margin: 10px;
    }

</style>

<div class="container-fluid m-0 p-0">
    <div class="row">
        <div class="col-9 p-0">
            <div id="map"></div>
        </div>
        <div class="col-3 p-0">
            <div class="panel">
                результат:<input style="width: 400px" id="rezult" >
            </div>
        </div>
    </div>
</div>


<script>
    // ymaps.ready(init);
    // //
    // function init() {
    //     var geolocation = ymaps.geolocation,
    //         myMap = new ymaps.Map('map', {
    //             center: [55, 34],
    //             zoom: 20
    //         }, {
    //             searchControlProvider: 'yandex#search'
    //         });
    //
    //     geolocation.get({
    //         provider: 'yandex',
    //         mapStateAutoApply: true
    //     }).then(function (result) {
    //         // Красным цветом пометим положение, вычисленное через ip.
    //         result.geoObjects.options.set('preset', 'islands#redCircleIcon');
    //         result.geoObjects.get(0).properties.set({
    //             balloonContentBody: 'Мое местоположение'
    //         });
    //         myMap.geoObjects.add(result.geoObjects);
    //     });
    //
    //     geolocation.get({
    //         provider: 'browser',
    //         mapStateAutoApply: true
    //     }).then(function (result) {
    //         // Синим цветом пометим положение, полученное через браузер.
    //         // Если браузер не поддерживает эту функциональность, метка не будет добавлена на карту.
    //         result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
    //         var a = myMap.geoObjects.add(result.geoObjects);
    //         var userAddress = result.geoObjects.get(0).properties.get('text');
    //         var userCoodinates = result.geoObjects.get(0).geometry.getCoordinates();
    //         // Пропишем полученный адрес в балуне.
    //         result.geoObjects.get(0).properties.set({
    //             balloonContentBody: 'Адрес: ' + userAddress +
    //                 '<br/>Координаты:' + userCoodinates
    //         });
    //         console.log(userAddress, "1")
    //         console.log(userCoodinates, "2")
    //     });
    //
    // }
    var el = document.getElementById('rezult');

    const copy = "© <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors";
    const url = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
    const osm = L.tileLayer(url, { attribution: copy });
    const map = L.map("map", { layers: [osm], minZoom: 5 });

    map.
    locate()
        .on("locationfound", (e) => map.setView( e.latlng, 12))
        .on("locationerror", () => map.setView([0, 0], 5))
        .on("locationfound",function (e) {
            const { lat, lng } = e.latlng;
            console.log(e.latlng,"ddqq");
            const get_addr_api = `https://nominatim.openstreetmap.org/reverse.php?lat=${lat}&lon=${lng}&zoom=18&format=jsonv2`;

            fetchAddressData(get_addr_api).then((data) => {
                var { city, house_number, road } = data.address;

                if (clickMarker != undefined) {
                    map.removeLayer(clickMarker);
                };

                clickMarker = L.marker([data.lat, data.lon],).addTo(map);

                var popupText =  city + ', ' + road
                if (house_number) {
                    popupText += ', ' + house_number
                }
                console.log(popupText,"ddqq");

                el.value = popupText;
                clickMarker.bindPopup(popupText).openPopup();

            });

            async function fetchAddressData(url) {
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    return data;
                } catch (err) {
                    console.error(err);
                }
            };
        });

    async function loadFacilities() {
        const facilities_url = `/api/facilities/?in_bbox=${map.getBounds().toBBoxString()}`
        const response = await fetch(facilities_url)
        const geojson = await response.json()
        return geojson
    }

    async function renderFacilities() {
        const facilities = await loadFacilities();
        L.geoJSON(facilities, {
            onEachFeature: function (feature) {

                var facilityMarkerStyle = L.Icon.extend({
                    options: {
                        iconSize: [20, 20],
                    }
                });

                var facilityMarker = new facilityMarkerStyle({ iconUrl: document.getElementById("facilityMarkerURL").innerHTML });

                var lat = feature.geometry.coordinates[1];
                var lon = feature.geometry.coordinates[0];

                L.marker([lat, lon], { icon: facilityMarker }).bindPopup(feature.properties.name).addTo(map);
            }
        });
    }

    map.on("moveend", renderFacilities);

    var clickMarker;

    map.on("click", function (e) {
        const { lat, lng } = e.latlng;
        console.log(e.latlng,"dd");
        const get_addr_api = `https://nominatim.openstreetmap.org/reverse.php?lat=${lat}&lon=${lng}&zoom=18&format=jsonv2`;

        fetchAddressData(get_addr_api).then((data) => {
            var { city, house_number, road } = data.address;

            if (clickMarker != undefined) {
                map.removeLayer(clickMarker);
            };

            clickMarker = L.marker([data.lat, data.lon],).addTo(map);

            var popupText = '' + city + ', ' + road
            if (house_number) {
                popupText += ', ' + house_number
            }
            // popupText += '</p>'
            el.value = popupText;

            clickMarker.bindPopup(popupText).openPopup();

        });

        async function fetchAddressData(url) {
            try {
                const response = await fetch(url);
                const data = await response.json();
                return data;
            } catch (err) {
                console.error(err);
            }
        };
    })
</script>

<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$url = $_SERVER['QUERY_STRING'];
$int = filter_var(($url), FILTER_SANITIZE_NUMBER_INT);

$arMonths_r = array();
for ($i = 1; $i <= 12; $i++)
	$arMonths_r[$i] = ToLower(GetMessage('MONTH_'.$i.'_S'));
?>
<?
if ($arParams['SHOW_FILTER'] == 'Y'):
?>
<table class="bx-users-toolbar"><tr><td class="bx-users-toolbar-last">
<form name="bx_birthday_filter" action="?action=value&department=" method="get">
    <?
    if ($arParams['bShowFilter'])
    {
        ?><div class="bx-birthday-officelink"><?
        if ($arResult['CURRENT_USER']['DEPARTMENT_TOP'])
        {
            if ($arResult['ONLY_MINE'] == 'Y')
            {
                ?><a href="<?echo $APPLICATION->GetCurPageParam('', array('department'))?>">показать для всей компании</a><br /><?
            }
            else
            {
                ?><a href="<?echo $APPLICATION->GetCurPageParam('department='.$arResult['CURRENT_USER']['DEPARTMENT_TOP'], array('department'))?>">показать для моего офиса</a><br /><?
            }
        }
        ?></div><?
    }?>
    <div class="bx-birthday-container">
        <?echo GetMessage('INTR_ISBN_TPL_FILTER_DEPARTMENT')?>:
        <select id="select-state" name="department"  style="">
            <?if(!empty($int)):?>
                <option value=""><?php echo $arParams["DEPARTMENT_REMUVE"][$int] ?></option>
            <?else: ?>
                <option value="">Ведите отдел...</option>
            <?endif;?>
            <?php foreach($arParams["DEPARTMENT_REMUVE"] as $k => $v): ?>
                <option value="<?php echo $k ?>"><?php echo $v ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="<?echo GetMessage('INTR_ISBN_TPL_FILTER_SUBMIT')?>" />
    </div>
</form>
<script type="text/javascript">
window.onload = function() {document.forms.bx_birthday_filter.department.onchange = function() {this.form.submit()}}
</script>
</td></tr></table>
<?
endif;
?>
<div class="bx-birthday-layout">
<?
foreach ($arResult['USERS'] as $arUser)
{
	$birthday = FormatDateEx(
		$arUser['PERSONAL_BIRTHDAY'],
		false,
		$arParams['DATE_FORMAT'.($arParams['SHOW_YEAR'] == 'Y' || $arParams['SHOW_YEAR'] == 'M' && $arUser['PERSONAL_GENDER'] == 'M' ? '' : '_NO_YEAR')]
	);

	if ($arUser['IS_BIRTHDAY']) $birthday .= ' - '.GetMessage('INTR_ISBN_TPL_TODAY');

	$arUser['SUBTITLE'] = $birthday;
	$arUser['SUBTITLE_FEATURED'] = $arUser['IS_BIRTHDAY'] ? 'Y' : 'N';

	$APPLICATION->IncludeComponent(
		'bitrix:intranet.system.person',
		'.default',
		array(
			'USER' => $arUser,
			'USER_PROPERTY' => $arParams['USER_PROPERTY'],
			'PM_URL' => $arParams['PM_URL'],
			'STRUCTURE_PAGE' => $arParams['STRUCTURE_PAGE'],
			'STRUCTURE_FILTER' => $arParams['STRUCTURE_FILTER'],
			'USER_PROP' => $arResult['USER_PROP'],
			'NAME_TEMPLATE' => $arParams['NAME_TEMPLATE'],
			'SHOW_LOGIN' => $arParams['SHOW_LOGIN'],
			"DATE_FORMAT" => $arParams["DATE_FORMAT"],
			"DATE_FORMAT_NO_YEAR" => $arParams["DATE_FORMAT_NO_YEAR"],
			"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
			"SHOW_YEAR" => $arParams["SHOW_YEAR"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
			"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
		),
		null,
		array('HIDE_ICONS' => 'Y')
	);
}
?>

<script >
    $(document).ready(function () {
        $('select').selectize({
            sortField: 'text'
        });
    });
</script>


