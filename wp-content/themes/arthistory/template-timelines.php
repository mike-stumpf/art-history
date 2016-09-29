<?php
/*
Template Name: Timelines
*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryTimelines = new artHistory\artHistory();
$timelines = $artHistoryTimelines->getTimelineData();
?>
    <!--sidebar-->
    <aside id="timelines-sidebar-container" class="show-for-large">
        <!-- dynamically populated -->
    </aside>
    <div id="timelines-sidebar-data-container" class="show-for-large">
        <!-- dynamically populated -->
    </div>
    <!--main content-->
    <main id="timelines-main-content" class="small-12 large-9 large-grid-end">
        <section id="timelines-header-image-container">
            <!-- timeline images-->
            <?php foreach($timelines as $timeline){
                if (!empty($timeline['image'])){ ?>
                    <img class="timeline-header-image faded-out l--show-for-<?php echo $timeline['slug'];?>" src="<?php echo $timeline['image'];?>" alt="<?php echo $timeline['title'];?>"/>
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
            <?php foreach($timelines as $timeline){?>
                <div id="<?php echo $timeline['slug'];?>" class="timeline l--show-for-<?php echo $timeline['slug'];?> faded-out" data-items-list="<?php echo $timeline['niceSlug'];?>"></div>
            <?php } ?>
        </section>
        <section id="timelines-selector-container" class="grid-container">
            <!--navigation-->
            <?php $i = 1;
            foreach($timelines as $timeline) {
                $className = $i===1?'active':'';
                $gridEnd = '';
                if(sizeof($timeline) === $i){
                    $gridEnd = ' grid-end';
                } ?>
                <div class="timelines-selector-block <?php echo $gridEnd;?>">
                    <a href="#" data-timeline-selector="<?php echo $i;?>" class="timelines-selector <?php echo $className;?>">
                        <?php echo $timeline['title']?>
                        <span class="timelines-selector-line"></span>
                        <span class="timelines-selector-indicator"></span>
                    </a>
                </div>
                <?php $i++;
            } ?>
        </section>
    </main>
    <a href="#timeline-mobile-modal" id="timeline-mobile-modal-trigger" class="faded-out"></a>
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
            events;
        <?php $i = 1;
        foreach($timelines as $timeline){?>
        timelineTitle<?php echo $i;?> = '<?php echo $timeline['niceSlug'];?>';
        events<?php echo $i;?> = "<?php echo addslashes(json_encode($timeline['timelineEvents']));?>";
        timeline<?php echo $i;?> = "<?php echo addslashes(json_encode($timeline));?>";
        timelineData[timelineTitle<?php echo $i;?>] = {
            title: '<?php echo $timeline['title'];?>',
            slug: '<?php echo $timeline['slug'];?>'
        };
        try {
            var unsortedEvents = JSON.parse(events<?php echo $i;?>);
            unsortedEvents.sort(function(a,b){
                return new Date(a.start) - new Date(b.start);
            });
            timelineData[timelineTitle<?php echo $i;?>].events = unsortedEvents;
        } catch (e){
            console.log(e);
            timelineData[timelineTitle<?php echo $i;?>].events = [];
        }
        try {
            timelineData[timelineTitle<?php echo $i;?>].timeline = JSON.parse(timeline<?php echo $i;?>);
        } catch (e){
            console.log(e);
            timelineData[timelineTitle<?php echo $i;?>].timeline = {};
        }
        <?php $i++;
        } ?>
    </script>
<?php get_footer();