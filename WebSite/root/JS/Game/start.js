'use strict'
//----------------------------------------------------------------------------------------------------
//--------------------------This Game is powered by Phaser 2.6.2 game engine--------------------------
//----------------------------------------------------------------------------------------------------
/** Initially I started using Phaser 3, but since the most of material and tutorials are about Phaser 2,
* I eventually had to switch using the previous version of Phaser and indeed it ended up being the
* best option due to a much quicker retrieving of examples, advises, and tutorials by a broader community.
* Several objects and prototypes have been used since classes are an EcmaScript2015 (*ES6) and above 
* feature and sadly any Phaser versions does support yet up to ES5 and older versions only.
* In the end I can say that Phaser 2 is a nice and powerful tool but most surely if I ever build I game
* again I would definitely go for Phaser 3 that time, since during my researches I find out it is for
* a plethora of reasons much better, performing, and amusing to work with, despite its earlier community.
*/



/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////// First State /////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

var Intro = {
    preload: function() {
        //loading images
        this.game.load.image('fullScreen'      , 'Assets/Game/fullScreen.png');
        this.game.load.image('blur'            , 'Assets/Game/backBlur.png');
        this.game.load.image('shadow'          , 'Assets/Game/button.png');
        this.game.load.atlasJSONHash('honei0'  , 'Assets/Game/honey/honey-0.png', 'Assets/Game/honey/honey-0.json');
        this.game.load.image('play'            , 'Assets/Game/play.png');
        this.game.load.image('blur'            , 'Assets/Game/backBlur.png');
        
    },

    create: function() {
        var width  = this.game.width;   // variable width
        var height = this.game.height; // variable height

        //setting the world scene size
        this.game.world.setBounds(0, 0, width, height);
        this.game.stage.backgroundColor = '#444';
        this.game.camera.scale.setTo(1,1);

        //adding the sprite background
        var bg  = this.game.add.sprite(0,0, 'honei0');
        this.bg = bg;
        bg.animations.add ('bg');
        bg.animations.play('bg', 2, true);
        var scaledWidth  = width/bg.scale.x;
        var scaledHeight = height/bg.scale.y;

        bg.anchor.setTo(0.5, 0.5);
        bg.x             = width/2 + (width/100*3);
        bg.y             = height/2 + (height/100*2);
        bg.width         = (height+width)/2+(((height+width)/2)/100*50);
        bg.height        = height+(height/100*5);
        bg.fixedToCamera = true;

        //adding "Play "button
        this.shadow = this.game.add.button(0,0, 'shadow', this.actionOnClick, this);
        this.play   = this.game.add.button(0,0, 'play', this.actionOnClick, this);
        //button setyle and settings
        toTheCenter(this.shadow);
        toTheCenter(this.play);

        function toTheCenter(sprite){
            sprite.anchor.setTo(0.5, 0.5);
            sprite.tint   = 0x00ff00;
            sprite.width  = width/100*20;
            sprite.height = height/100*20;
            sprite.x      = width/2; //- this.button.width/2;
            sprite.y      = height/2; //- this.button.height/2;
            sprite.scale.setTo(0.05,0.05);
            sprite.fixedToCamera = true;
        }
        //starting the button animation
        this.buttonTween(this.shadow);
        this.buttonTween(this.play);
        

        //getting al the global setting
        this.global  = new GlobalCreate(this.game, width,height);
        this.curtain = this.global.curtain;
        //triggering the intro blurry animation
        this.curtain.visible = true;
        var curtainTween = this.game.add.tween(this.curtain).to( { alpha: 0 }, 1000, "Linear", true);
        curtainTween.onComplete.add(()=>{this.curtain.visible = false}, this); //make curtain intouchable

        //hiding the superflous
        this.global.labelScore.visible  = false;
        this.global.levelNumber.visible = false;
    },

    update: function() {
    },

    buttonTween: function (sprite){
        //play button bouncing animation
        var bta = this.game.add.tween(sprite);
        var bts = this.game.add.tween(sprite.scale);
        bta.to({alpha:[0.8,0.4]},1000,Phaser.Easing.Bounce.InOut,true, 0, Number.MAX_VALUE);
        bts.to({x:[0.1,0.07], y:[0.1,0.07]},1000,Phaser.Easing.Bounce.InOut,true, 0, Number.MAX_VALUE);
    },

    /**
    * Doing some background animation after pressing
    * the button, before strating the actual game
    */
    actionOnClick: function (){
        var startFade = this.game.add.tween(this.bg);
        startFade.to( { alpha: 0 }, 1500, Phaser.Easing.Exponential.InOut, true);
        startFade = this.game.add.tween(this.bg.scale);
        startFade.to( { x: 6, y:6 }, 1500, Phaser.Easing.Exponential.InOut, true);
        startFade.onComplete.add(()=>{this.game.state.start('TheGame')}, this); //make curtain intouchable
    }
}