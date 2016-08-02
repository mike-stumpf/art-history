(function(){

    var that = artHistory.mediaQueries,
        tabletWidth = 768,//match to sass file
        desktopWidth = 1024;

    //core

    this.getWindowWidth = function(){
        //http://stackoverflow.com/questions/1248081/get-the-browser-viewport-dimensions-with-javascript
        return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    };

    this.getWindowHeight = function(){
        return Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    };

    //media queries

    this.isMobile = function(){
        return that.getWindowWidth() < tabletWidth;
    };

    this.isTablet = function(){
        return that.getWindowWidth() >= tabletWidth && that.getWindowWidth() < desktopWidth;
    };

    this.isDesktop = function(){
        return that.getWindowWidth() >= desktopWidth;
    };


}).apply(artHistory.mediaQueries);