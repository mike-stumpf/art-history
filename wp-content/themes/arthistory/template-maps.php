<?php
/*
Template Name: Maps

//todo, documentation

*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryMaps = new artHistory\artHistory();
$mapGroups = $artHistoryMaps->getMapData();
?>
    <section id="maps-header-image-container">
        <?php foreach($mapGroups as $mapGroup){?>
            <img class="map-header-image faded-out l--show-for-<?php echo $mapGroup['slug'];?>" src="<?php echo $mapGroup['timeline']['image'];?>" alt="<?php echo $mapGroup['title'];?>"/>
        <?php } ?>
    </section>
    <section id="maps-timeline-container">
        <?php foreach($mapGroups as $mapGroup){?>
            <div id="timeline-<?php echo $mapGroup['slug'];?>" class="map-timeline l--show-for-<?php echo $mapGroup['slug'];?> faded-out" data-items-list="<?php echo $mapGroup['niceSlug'];?>"></div>
        <?php } ?>
    </section>
    <section id="maps-selector-container">
        <?php $i = 1;
        foreach($mapGroups as $mapGroup) { ?>
            <a href="#" data-timeline-selector="<?php echo $i;?>" class="maps-timeline-selector"><?php echo $mapGroup['title']?></a>
            <?php $i++;
        } ?>
    </section>
    <section id="maps-sidebar-container"></section>
    <script type="text/javascript">
        var mapData = {},
            mapTitle,
            timeline,
            events;
        <?php foreach($mapGroups as $mapGroup){?>
        mapTitle = '<?php echo $mapGroup['niceSlug'];?>';
        events = '<?php echo json_encode($mapGroup['events']);?>';
        timeline = '<?php echo json_encode($mapGroup['timeline']);?>';
        mapData[mapTitle] = {
            title: '<?php echo $mapGroup['title'];?>',
            slug: '<?php echo $mapGroup['slug'];?>'
        };
        try {
            mapData[mapTitle].events = JSON.parse(events);
        } catch (e){
            console.log(e);
            mapData[mapTitle].events = [];
        }
        try {
            mapData[mapTitle].timeline = JSON.parse(timeline);
        } catch (e){
            console.log(e);
            mapData[mapTitle].timeline = {};
        }
        <?php } ?>
    </script>
<?php get_footer();