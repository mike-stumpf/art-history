<?php
/*
Template Name: Timeline

//todo, documentation

*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryMaps = new artHistory\Maps();
$mapGroups = $artHistoryMaps->getMapData();
?>
    <section id="map-header-image-container">
        <?php foreach($mapGroups as $mapGroup){?>
            <img class="map-header-image faded-out l--show-for-<?php echo $mapGroup->slug;?>" src="<?php echo $mapGroup->timeline->image;?>" alt="<?php echo $mapGroup->name;?>"/>
        <?php } ?>
    </section>
    <section id="map-timeline-container">
        <?php foreach($mapGroups as $mapGroup){?>
            <div id="timeline-<?php echo $mapGroup->slug;?>" class="map-timeline l--show-for-<?php echo $mapGroup->slug;?> faded-out" data-items-list="<?php echo $mapGroup->niceSlug;?>"></div>
        <?php } ?>
    </section>
    <script type="text/javascript">
        var mapData = {},
            mapName,
            timeline,
            events;
        <?php foreach($mapGroups as $mapGroup){?>
        mapName = '<?php echo $mapGroup->niceSlug;?>';
        events = '<?php echo json_encode($mapGroup->events);?>';
        timeline = '<?php echo json_encode($mapGroup->timeline);?>';
        mapData[mapName] = {
            name: '<?php echo $mapGroup->name;?>',
            slug: '<?php echo $mapGroup->slug;?>'
        };
        try {
            mapData[mapName].events = JSON.parse(events);
        } catch (e){
            console.log(e);
            mapData[mapName].events = [];
        }
        try {
            console.log(typeof timeline);
            mapData[mapName].timeline = JSON.parse(timeline);
        } catch (e){
            console.log(e);
            mapData[mapName].timeline = {};
        }
        <?php } ?>
    </script>
<?php foreach($mapGroups as $mapGroup) {
    echo'<br/><br/><br/><br/>';
//    var_dump($mapGroup);
}
get_footer();