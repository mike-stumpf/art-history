<?php
/*
Template Name: Timeline

*/
get_header();

include_once('logic/bootstrapper.php');
$artHistoryTimeline = new artHistoryTimeline();
$timelineGroups = $artHistoryTimeline->getTimelineData();
?>
    <section id="map-header-image-container">
        <?php foreach($timelineGroups as $timelineGroup){?>
            <img class="map-header-image faded-out l--show-for-<?php echo $timelineGroup->slug;?>" src="<?php echo $timelineGroup->timeline->image;?>" alt="<?php echo $timelineGroup->name;?>"/>
        <?php } ?>
    </section>
    <section id="map-timeline-container">
        <?php foreach($timelineGroups as $timelineGroup){?>
            <div id="timeline-<?php echo $timelineGroup->slug;?>" class="map-timeline l--show-for-<?php echo $timelineGroup->slug;?> faded-out" data-items-list="<?php echo $timelineGroup->niceSlug;?>Items"></div>
        <?php } ?>
    </section>
    <script type="text/javascript">
        <?php foreach($timelineGroups as $timelineGroup){?>
        var dataKey = '<?php echo $timelineGroup->niceSlug;?>Items',
            dataValue = '<?php echo json_encode($timelineGroup->events);?>';
        eval('var '+dataKey+'='+dataValue);//convert php object to json object
        <?php } ?>
    </script>
<?php foreach($timelineGroups as $timelineGroup) {
    echo'<br/><br/><br/><br/>';
//    var_dump($timelineGroup);
}
get_footer();