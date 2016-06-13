<?php
/*
Template Name: Timeline

//todo, documentation

*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryTimeline = new artHistory\Timeline();
$timelineGroups = $artHistoryTimeline->getTimelineData();
?>
    <section id="map-header-image-container">
        <?php foreach($timelineGroups as $timelineGroup){?>
            <img class="map-header-image faded-out l--show-for-<?php echo $timelineGroup->slug;?>" src="<?php echo $timelineGroup->timeline->image;?>" alt="<?php echo $timelineGroup->name;?>"/>
        <?php } ?>
    </section>
    <section id="map-timeline-container">
        <?php foreach($timelineGroups as $timelineGroup){?>
            <div id="timeline-<?php echo $timelineGroup->slug;?>" class="map-timeline l--show-for-<?php echo $timelineGroup->slug;?> faded-out" data-items-list="<?php echo $timelineGroup->niceSlug;?>"></div>
        <?php } ?>
    </section>
    <script type="text/javascript">
        var mapData = {},
            mapName,
            timeline,
            events;
        <?php foreach($timelineGroups as $timelineGroup){?>
        mapName = '<?php echo $timelineGroup->niceSlug;?>';
        events = '<?php echo json_encode($timelineGroup->events);?>';
        timeline = '<?php echo json_encode($timelineGroup->timeline);?>';
        mapData[mapName] = {
            name: '<?php echo $timelineGroup->name;?>',
            slug: '<?php echo $timelineGroup->slug;?>'
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
<?php foreach($timelineGroups as $timelineGroup) {
    echo'<br/><br/><br/><br/>';
//    var_dump($timelineGroup);
}
get_footer();