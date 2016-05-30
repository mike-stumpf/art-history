<?php
/*
Template Name: Homepage

*/
get_header();

include_once('logic/bootstrapper.php');
$artHistoryTimeline = new artHistoryTimeline();
$timelineGroups = $artHistoryTimeline->getTimelineData();
?>
    <section id="map-header-image-container">
        <?php foreach($timelineGroups as $timelineGroup){?>
            <img class="map-header-image faded-out l--show-for-<?php echo $timelineGroup->slug;?>" src="<?php echo $timelineGroup->timeline->image;?>" alt="<?php echo $timelineGroup->name;?>"/>
        <?php }?>
    </section>
    <section id="map-timeline-container">
        <div id="map-timeline-1" class="map-timeline l--show-for-map-1 faded-out" data-items-list="map1Items"></div>
        <div id="map-timeline-2" class="map-timeline l--show-for-map-2 faded-out" data-items-list="map2Items"></div>
        <div id="map-timeline-3" class="map-timeline l--show-for-map-3 faded-out" data-items-list="map3Items"></div>
    </section>
    <script type="text/javascript">
        var map1Items = [
                {id: 1, content: 'item 1', image:'https://placeholdit.imgix.net/~text?txtsize=25&txt=50%C3%9750&w=50&h=50', start: '2013-04-20'},
                {id: 2, content: 'item 2', image:'https://placeholdit.imgix.net/~text?txtsize=25&txt=50%C3%9750&w=50&h=50', start: '2013-04-14'},
                {id: 3, content: 'item 3', image:'https://placeholdit.imgix.net/~text?txtsize=25&txt=50%C3%9750&w=50&h=50', start: '2013-04-18'},
                {id: 4, content: 'item 4', start: '2013-04-16', end: '2013-04-19'},
                {id: 5, content: 'item 5', image:'https://placeholdit.imgix.net/~text?txtsize=25&txt=50%C3%9750&w=50&h=50', start: '2013-04-25'},
                {id: 6, content: 'item 6', image:'https://placeholdit.imgix.net/~text?txtsize=25&txt=50%C3%9750&w=50&h=50', start: '2013-04-27'}
            ],
            map2Items = [],
            map3Items = [];
    </script>
<?php
foreach($timelineGroups as $timelineGroup) {
    echo'<br/><br/><br/><br/>';
    var_dump($timelineGroup);
}
get_footer();