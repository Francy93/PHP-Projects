/** 
 * GLOBAL PARAMETERS
 */

var GlobalCreate = function(game, width, height) {
    this.game = game;
    //Blur after the level completion
    this.curtain               = this.game.add.sprite(width/2, height/2, 'blur');
    this.curtain.anchor.setTo  ( 0.5, 0.5 );
    this.curtain.height        = height;
    this.curtain.width         = width;
    this.curtain.setScaleMinMax( 0.6, 0.6, 1,1 );
    this.curtain.scaleMax      = false;
    this.curtain.fixedToCamera = true;
    this.curtain.visible       = false;

    //Score label style
    var style = { font: "25px Arial", fill: "#ffffff", align: "center", stroke: "#000000", strokeThickness: 2};
    this.style = style;
    //including the score text
    var lCorner = 15;
    this.labelScore         = this.game.add.text(lCorner, lCorner, "scoreTextHere", style);
    this.labelScore.centerX = width/2;
    this.labelScore.centerY = height/2-20;
    this.labelScore.alpha   = 1;
    this.labelScore.cameraOffset.setTo(this.labelScore.x, this.labelScore.y);
    this.labelScore.setScaleMinMax(1, 1); //fixing the labelScore scale
    var labAnim = this.game.add.tween(this.labelScore.cameraOffset);
    labAnim.to({x:lCorner, y:lCorner},3000,Phaser.Easing.Quintic.InOut,true,1000);
    //score opacity
    this.game.add.tween(this.labelScore).to({alpha: 0.3},3000,Phaser.Easing.Quintic.InOut,true,1000);
    this.labelScore.fixedToCamera = true;
    this.labelScore.score = this.score;

    //level text
    var levelText            = "Ready for level "+ this.game.levels+"?";
    this.levelNumber         = this.game.add.text(0,0, levelText,style)
    this.levelNumber.x       = width / 2 - this.labelScore.width/2;
    this.levelNumber.centerY = height/2 + 30;
    this.levelNumber.alpha   = this.game.levels ==1? 0:1;//if a first start then text remain invisible
    this.game.add.tween(this.levelNumber).to({alpha: 0},1000,Phaser.Easing.Quintic.InOut,true);
    this.levelNumber.fixedToCamera = true;
    this.levelNumber.setScaleMinMax(0.8, 0.8);

    //fullscreen button
    this.fullScreen       = this.game.add.button(width-65, height-60, 'fullScreen', actionOnClick, this);
    this.fullScreen.alpha = 0.2;               //setting button transparency
    this.fullScreen.anchor.setTo  (0.2,0.2);   //setting the fixed position
    this.fullScreen.scale.setTo   (0.2,0.2);   //setting the scale
    this.fullScreen.setScaleMinMax(0.2,  1);   //setting the Min and Max scale
    this.fullScreen.fixedToCamera = true;      //fixed to camera
    this.fullScreen.onInputOver.add(over, this);
    this.fullScreen.onInputOut.add(out, this);
    this.fullScreen.onInputUp.add(up, this);
    var alphaOut  = false;
    var anchorOut = false;
    //if a click up is detected then animate fullscreen button
    function up() {
        var config = {y: [0.3,0.2], x: [0.3,0.2]};
        this.game.add.tween(this.fullScreen).stop();
        this.game.add.tween(this.fullScreen.scale).to(config,1000,Phaser.Easing.Bounce.Out,true);
    } 
    //if the pointer is over the button then animate fullscreen button
    function over() {
        if (alphaOut.isRunning) {alphaOut.stop();}
        if (anchorOut.isRunning) {anchorOut.stop();}
        this.game.add.tween(this.fullScreen).to({alpha: 1},500,"Linear",true); 
        this.game.add.tween(this.fullScreen.anchor).to({y: 0.4,},200,"Linear",true);
    }
    //if the pointer get out the button then animate fullscreen button
    function out() {
        alphaOut  = this.game.add.tween(this.fullScreen);
        anchorOut = this.game.add.tween(this.fullScreen.anchor);
        alphaOut.to({alpha: 0.2},1000,"Linear",true);
        anchorOut.to({y: 0.2,},800,Phaser.Easing.Bounce.Out,true);
    }
    //if the fullscreen button is clicked then animate the button and get full screen
    function actionOnClick () {
        if(this.game.scale.isFullScreen){
            this.game.scale.stopFullScreen();
            this.game.add.tween(this.fullScreen).to({alpha: 0.2},1000,"Linear",true);
            this.game.add.tween(this.fullScreen.anchor).to({y: 0.2,},800,Phaser.Easing.Bounce.Out,true);
        }else {
            this.game.scale.startFullScreen();
            this.game.add.tween(this.fullScreen).to({alpha: 0.2},1000,"Linear",true);
            this.game.add.tween(this.fullScreen.anchor).to({y: 0.2,},800,Phaser.Easing.Bounce.Out,true);
        }
    }
    //fullScreen mode
    this.game.scale.fullScreenScaleMode = Phaser.ScaleManager.EXACT_FIT;
    //this.game.scale.onFullScreenChange.add(onFullScreenChange, this);
}

////////////////////////////////////////////////// UTILS //////////////////////////////////////////////////////////

const Util = {
    /**
     * Generate a random number within a closed range
     */
    randomInt: function(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    /**
     * Calculate distance between two points
     */
    distanceFormula: function(x1, y1, x2, y2) {
        var withinRoot = Math.pow(x1-x2,2) + Math.pow(y1-y2,2);
        var dist       = Math.pow(withinRoot,0.5);
        return dist;
    },
    /**
    * Generate a random float number within a closed range
    */
    randomFloat: function(min, max, game) {
        var positive = game.rnd.realInRange(min, max);
        var negative = game.rnd.realInRange(-min, -max);
        return Math.floor(Math.random()*2)>0 ? positive : negative;
    }
};
