<?php
/*
Template Name: Timelines
*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryTimelines = new artHistory\artHistory();
$timelines = $artHistoryTimelines->getTimelineData();
$defaultTimeline = get_option('ah_default_timeline');
$selectedTimelineIndex = 0;
?>
    <!--sidebar-->
    <aside id="timelines-sidebar-container" class="show-for-large">
        <!-- dynamically populated -->
    </aside>
    <div id="timelines-sidebar-data-container" class="show-for-large">
        <!-- dynamically populated -->
    </div>
    <!--main content-->
    <main id="timelines-main-content" class="col-sm-12 col-lg-offset-3 col-lg-9">
        <section id="timelines-header-image-container">
            <!-- timeline images-->
            <?php foreach($timelines as $index=>$timeline){
                if ($timeline['slug'] === $defaultTimeline) {
                    $selectedTimelineIndex = $index;
                }
                if (!empty($timeline['image'])){ ?>
                    <img class="timeline-header-image faded-out l--show-for-timeline-<?php echo $index;?>" src="<?php echo $timeline['image'];?>" alt="<?php echo $timeline['title'];?>"/>
                <?php }
            } ?>
            <!-- timeline title-->
            <div id="timelines-header-title-container" class="timelines-header-title-element-container">
                <div id="timelines-header-title-left" class="timelines-header-title-element-container timelines-header-title-bullet">
                    <div class="timelines-header-title-element">
                        &diams;
                    </div>
                </div>
                <div class="timelines-header-title-element">
                    <h3 id="timelines-header-title"></h3>
                </div>
                <div id="timelines-header-title-right" class="timelines-header-title-element-container timelines-header-title-bullet">
                    <div class="timelines-header-title-element">
                        &diams;
                    </div>
                </div>
            </div>
        </section>
        <section id="timelines-timeline-container">
            <!--timelines-->
            <?php foreach($timelines as $index=>$timeline){?>
                <div id="timeline-<?php echo $index;?>" class="timeline l--show-for-timeline-<?php echo $index;?> faded-out" data-items-list="<?php echo $timeline['niceSlug'];?>"></div>
            <?php } ?>
        </section>
        <section id="timelines-selector-container" class="grid-container row">
            <!--navigation-->
            <?php foreach($timelines as $index=>$timeline) {
                $className = $index===$selectedTimelineIndex?'active':''; ?>
                <div class="timelines-selector-block col">
                    <a href="#" data-timeline-selector="<?php echo $index;?>" class="timelines-selector <?php echo $className;?>">
                        <?php echo $timeline['navigationName']?>
                        <span class="timelines-selector-line"></span>
                        <span class="timelines-selector-indicator"></span>
                    </a>
                </div>
            <?php } ?>
        </section>
    </main>
    <a href="#timeline-mobile-modal" id="timeline-mobile-modal-trigger" class="faded-out">Modal Trigger</a>
    <section class="modal--fade" id="timeline-mobile-modal" data-stackable="false" tabindex="-1" role="dialog" aria-labelledby="label-fade" aria-hidden="true">
        <div id="timeline-mobile-modal-content" class="modal-inner">
            <!-- dynamically populated -->
        </div>
        <a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal" data-close="Close">&times;</a>
    </section>
    <!--fullscreen overlays-->
    <div id="image-zoom-container" class="full-screen-takeover faded-out"></div>
    <div id="image-zoom-overlay" class="full-screen-takeover faded-out"></div>
    <div id="timelines-transition-overlay" class="full-screen-takeover"></div>
    <script type="text/javascript">
        var timelineData = {},
            timelineTitle,
            timeline,
            events,
            movements,
            selectedTimelineIndex = <?php echo $selectedTimelineIndex;?>;
        <?php foreach($timelines as $index=>$timeline){?>
        timelineTitle<?php echo $index;?> = '<?php echo $timeline['niceSlug'];?>';
        events<?php echo $index;?> = "<?php echo addslashes(json_encode($timeline['timelineEvents']));?>";
        timeline<?php echo $index;?> = "<?php echo addslashes(json_encode($timeline));?>";
        movements<?php echo $index;?> = "<?php echo addslashes(json_encode($timeline['movements']));?>";
        timelineData[timelineTitle<?php echo $index;?>] = {
            title: '<?php echo $timeline['title'];?>',
            slug: '<?php echo $timeline['slug'];?>'
        };
        try {
            var unsortedEvents = JSON.parse(events<?php echo $index;?>);
            unsortedEvents.sort(function(a,b){
                return new Date(a.start) - new Date(b.start);
            });
            timelineData[timelineTitle<?php echo $index;?>].events = unsortedEvents;
        } catch (e){
            console.warn(e);
            timelineData[timelineTitle<?php echo $index;?>].events = [];
        }
        try {
            timelineData[timelineTitle<?php echo $index;?>].timeline = JSON.parse(timeline<?php echo $index;?>);
        } catch (e){
            console.warn(e);
            timelineData[timelineTitle<?php echo $index;?>].timeline = {};
        }
        try {
            var unsortedMovements = JSON.parse(movements<?php echo $index;?>);
            unsortedMovements.sort(function(a,b){
                return new Date(a.start) - new Date(b.start);
            });
            timelineData[timelineTitle<?php echo $index;?>].movements = unsortedMovements;
        } catch (e){
            console.warn(e);
            timelineData[timelineTitle<?php echo $index;?>].movements = {};
        }
        <?php } ?>
    </script>
<?php get_footer();