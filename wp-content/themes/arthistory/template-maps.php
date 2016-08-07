<?php
/*
Template Name: Maps
*/
get_header();

include_once('php/bootstrapper.php');
$artHistoryMaps = new artHistory\artHistory();
$mapGroups = $artHistoryMaps->getMapData();
?>
    <!--sidebar-->
    <aside id="maps-sidebar-container" class="show-for-large">
        <!-- dynamically populated -->
    </aside>
    <div id="maps-sidebar-event-container" class="show-for-large">
        <!-- dynamically populated -->
    </div>
    <!--main content-->
    <main id="maps-main-content" class="small-12 large-9 large-grid-end">
        <section id="maps-header-image-container">
            <!-- map images-->
            <?php foreach($mapGroups as $mapGroup){
                if (!empty($mapGroup['timeline']['image'])){ ?>
                    <img class="map-header-image faded-out l--show-for-<?php echo $mapGroup['slug'];?>" src="<?php echo $mapGroup['timeline']['image'];?>" alt="<?php echo $mapGroup['title'];?>"/>
                <?php }
            } ?>
            <!-- map title-->
            <div id="maps-header-title-container" class="maps-header-title-element-container">
                <div id="maps-header-title-left" class="maps-header-title-element-container maps-header-title-bullet">
                    <div class="maps-header-title-element">
                        &diams;
                    </div>
                </div>
                <div class="maps-header-title-element">
                    <h3 id="maps-header-title"></h3>
                </div>
                <div id="maps-header-title-right" class="maps-header-title-element-container maps-header-title-bullet">
                    <div class="maps-header-title-element">
                        &diams;
                    </div>
                </div>
            </div>
        </section>
        <section id="maps-timeline-container">
            <!--timelines-->
            <?php foreach($mapGroups as $mapGroup){?>
                <div id="timeline-<?php echo $mapGroup['slug'];?>" class="map-timeline l--show-for-<?php echo $mapGroup['slug'];?> faded-out" data-items-list="<?php echo $mapGroup['niceSlug'];?>"></div>
            <?php } ?>
        </section>
        <section id="maps-selector-container" class="grid-container">
            <!--navigation-->
            <?php $i = 1;
            foreach($mapGroups as $mapGroup) {
                $className = $i===1?'active':'';
                $gridEnd = '';
                if(sizeof($mapGroups) === $i){
                    $gridEnd = ' grid-end';
                } ?>
                <div class="maps-timeline-selector-block <?php echo $gridEnd;?>">
                    <a href="#" data-timeline-selector="<?php echo $i;?>" class="maps-timeline-selector <?php echo $className;?>">
                        <?php echo $mapGroup['title']?>
                        <span class="maps-timeline-selector-line"></span>
                        <span class="maps-timeline-selector-indicator"></span>
                    </a>
                </div>
                <?php $i++;
            } ?>
        </section>
    </main>
    <a href="#maps-timeline-mobile-modal" id="maps-timeline-mobile-modal-trigger" class="faded-out"></a>
    <section class="modal--fade" id="maps-timeline-mobile-modal" data-stackable="false" tabindex="-1" role="dialog" aria-labelledby="label-fade" aria-hidden="true">
        <div id="maps-timeline-mobile-modal-content" class="modal-inner">
            <!-- dynamically populated -->
        </div>
        <a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal" data-close="Close">&times;</a>
    </section>
    <!--transition overlay-->
    <div id="maps-transition-overlay"></div>
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