'use strict'

//////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////// Second State /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////


var GameCode = {
    preload: function() {
        //loading images
        this.game.load.image('background' , 'Assets/Game/bg.jpg');
        this.game.load.image('module'     , 'Assets/Game/module.png');
        this.game.load.image('particle'   , 'Assets/Game/particle.png');
        this.game.load.image('shadow'     , 'Assets/Game/shadow.png');
        this.game.load.image('pupil'      , 'Assets/Game/pupil.png');
    	this.game.load.image('iris'       , 'Assets/Game/iris.png');
        this.game.load.image('fullScreen' , 'Assets/Game/fullScreen.png');
        this.game.load.image('blur'       , 'Assets/Game/backBlur.png');
        this.game.load.image('exitCurtain', 'Assets/Game/GameOverBG.png');
    },

    create: function() {
        var width  = this.game.width;   // variable width
        var height = this.game.height;  // variable height
        //state of the game is not ended but just started then state.end=0;
        this.game.end = 0;
        //if not the first level, then don't overwrite the score
        this.score = this.score>0? this.score: 0; //score variable
        this.initialScore = this.score;
        this.game.levels = this.game.levels? this.game.levels : 1;
        //setting the world scene size
        this.game.world.setBounds(-width, -height, width*2, height*2);
    	this.game.stage.backgroundColor = '#444';

        //adding the tileSprite of the background
        this.game.add.tileSprite(-width, -height, this.game.world.width, this.game.world.height, 'background');

        //initializing physics and groups
        this.game.physics.startSystem(Phaser.Physics.P2JS);
        this.foodGroup = this.game.add.group();
        this.snakeHeadCollisionGroup = this.game.physics.p2.createCollisionGroup();
        this.foodCollisionGroup = this.game.physics.p2.createCollisionGroup();
       
        this.game.snakes = [];

        //creating the player
        var randX = Util.randomFloat(0,width/2,this.game);
        var randY = Util.randomFloat(0,height/2,this.game);
        var snake = new PlayerSnake(this.game, 'module', randX,randY);
        this.game.camera.follow(snake.head);

        this.snake = snake;
        
        //adding food randomly
        for (var i = 0 ; i < 50 ; i++) {
            this.foodParticle();
        }
        //initialize snake and collision
        var snake = this.game.snakes[0];
            snake.head.body.setCollisionGroup(this.snakeHeadCollisionGroup);
            snake.head.body.collides([this.foodCollisionGroup]);
            
            //callback for when the snake is destroyed
            snake.addDestroyedCallback(this.snakeDestroyed, this);      

        //fullscreen button and labels
        this.global          = new GlobalCreate(this.game, width, height);
        this.global.labelScore.score = this.score;
        this.curtain         = this.global.curtain;
        this.curtain.visible = true;

        //triggering the intro blurry animation
        var curtainTween = this.game.add.tween(this.curtain).to( { alpha: 0 }, 1000, "Linear", true);
        curtainTween.onComplete.add(()=>{this.curtain.visible = false}, this); //make curtain intouchable

        //fading before gameOver
        this.exitCurtain         = this.game.add.sprite(width/2, height/2, 'exitCurtain');
        this.exitCurtain.anchor.setTo(0.5, 0.5);
        this.exitCurtain.width   = width;
        this.exitCurtain.height  = height;
        this.exitCurtain.fixedToCamera = true;
        this.exitCurtain.alpha   = 0;
        this.exitCurtain.visible = false;

        //camera scaling variables
        this.initialHeadScale = this.snake.head.scale.x;
        this.maxScaling       = this.game.width/this.game.world.width;
        this.game.camera.tempScale = 1;
        this.firstRescale     = this.game.camera.tempScale + 0.3; //scale to this when the first food is eaten

        //final exit animations
        this.curtainTween     = this.game.add.tween(this.curtain);
        this.exitCurtainTween = this.game.add.tween(this.exitCurtain);
    },

    //-----------------
    // Main update loop
    //-----------------
    update: function() {
        //update game components
        for (var i = this.game.snakes.length - 1 ; i >= 0 ; i--) {
            this.game.snakes[i].update();
        }
        for (var i = this.foodGroup.children.length - 1 ; i >= 0 ; i--) {
            var f = this.foodGroup.children[i];
            //the function iside this "if condition" returns a bulean, if the snake eat then add a new particle
            if(f.food.update()){
                this.nextLevel();       //if score gool (+1500) is reachet then next level
                this.foodParticle();    //pushing a new particle
                this.updateScore();     //score increasing
                this.cameraScaling();   //camera zoom decreasing
            }
        }//score updating
        this.global.labelScore.text ="Your score: "+ ~~this.global.labelScore.score;
         //camera zoom updating
        this.game.camera.scale.setTo(this.game.camera.tempScale,this.game.camera.tempScale);
        //condition to exit the current level
        this.nextLevel();
        //condition to game over
        this.gameOver();
    },

    //decreasing camera zoom
    cameraScaling: function() {
        if(this.snake.alive){
            var tScale = this.firstRescale - (this.snake.head.scale.x - this.initialHeadScale);
            if (tScale > this.maxScaling){
                this.game.camera.scaled = true;
                this.game.add.tween(this.game.camera).stop();
                this.game.add.tween(this.game.camera).to({tempScale: tScale},1000,"Linear",true);
            }else if(tScale < this.maxScaling && this.game.camera.tempScale != this.maxScaling){
                this.game.add.tween(this.game.camera).to({tempScale: this.maxScaling},1000,"Linear",true);
                //adjusting the curtain scale based on the camera scale
                this.curtain.scale.x =0.6 / this.camera.scale.x-0.1 ;
                this.curtain.scale.y =0.6 / this.camera.scale.y-0.1 ;
            }
        }
    },

    //function to create a food particle
    foodParticle: function() {
        var width  = this.game.width;   //variable width
        var height = this.game.height;  //variable height
        this.initFood(Util.randomInt(-width, width), Util.randomInt(-height, height));
    },

    //Increasing the score
    updateScore: function(){
        //if score is less then the initial one + 1500
        if(this.score < this.initialScore+1500){
            //this increment the score value by 10
            this.score = this.snake.alive? this.score+10: 0;
            //score display delay 1 sec
            this.game.add.tween(this.global.labelScore).to({score: this.score},1000,"Linear",true);
        } else {this.global.labelScore.score = this.score}
    },

    //Create a piece of food at a point
    initFood: function(x, y) {
        var f = new Food(this.game, x, y);
        f.sprite.body.setCollisionGroup(this.foodCollisionGroup);
        this.foodGroup.add(f.sprite);
        f.sprite.body.collides([this.snakeHeadCollisionGroup]);
        return f;
    },

    //get food out of Snake
    snakeDestroyed: function(snake) {
        //the line here below make particles appear without bouncing effect
        //and determines the end of the game
        if(snake.player && this.game.end === 0){ //if the snake destroied is the player
            this.game.camera.shake(0.02, 300);
            this.game.fianlScore    = this.score;
            this.game.finalLevel    = this.game.levels;
            this.game.levels        = 1;
            snake.alive             = false;
            this.updateScore();
            this.game.end           = 0.01; 
            this.game.camera.scaled = false;
            //and of the game in 6 seconds
            this.game.add.tween(this.game).to({end: 1},6000,"Linear",true);
            
        }
        //place food where snake was destroyed
        for (var i = 0 ; i < snake.headPath.length ;
        i += Math.round(snake.headPath.length / snake.snakeLength) * 2) {
            this.initFood(
                snake.headPath[i].x + Util.randomInt(-10,10),
                snake.headPath[i].y + Util.randomInt(-10,10)
            );
        }
    },

    //here will be start the procedure to switch to the next level
    nextLevel: function() {
        //if the level started to end
        if(this.game.end >= 1 && this.snake.alive){
            this.game.camera.scaled = false;
            this.game.end = 0;
            this.game.state.start('TheGame');
                //if the Snake reaaches the score to pass the level
        } else if(this.global.labelScore.score >= this.initialScore + 1500 && this.game.end ===0){
            this.game.levels ++; //increase the level number
            this.game.end        = 0.1;
            this.curtain.alpha   = 0;
            this.curtain.visible = true;
            this.global.levelNumber.text = "Ready for level "+this.game.levels+"?";
            //fading the screen before new level stars
            this.game.add.tween(this.curtain).to( { alpha: 1 }, 2000, "Linear", true);
            this.game.add.tween(this.game).to({end: 1},6000,"Linear",true);
            this.game.add.tween(this.global.labelScore).to(
                {alpha: 1},2000,Phaser.Easing.Quintic.InOut,true);
            var camOffX = this.game.width/2  - this.global.labelScore.width/2;
            var camOffY = this.game.height/2 - this.global.labelScore.height/2 -20;
            this.game.add.tween(this.global.labelScore.cameraOffset).to(
                {x:camOffX, y:camOffY},2000,Phaser.Easing.Quintic.InOut,true);
            this.game.add.tween(this.global.levelNumber).to(
                {alpha: 1},2000,Phaser.Easing.Quintic.InOut,true,2000);
          }
    },

    gameOver: function() {
        //exiting the game
        if(!this.snake.alive && this.game.end === 1){
            this.game.camera.scaled = false;
            this.game.end = 0;
            this.game.state.start('GameOver');
        //these operations below allow final fade animation
        }else if(!this.snake.alive){ //fading the screen before exiting the game
            if (!this.curtainTween.isRunning && !this.exitCurtainTween.isRunning ) {
                //this condition is true when both of the animations are stopped
                this.exitCurtain.visible = true;
                this.curtain.visible     = true;
                //getting camera scale back to the normal
                this.game.add.tween(this.game.camera).to({tempScale: 1},3000,"Linear",true)
                this.game.add.tween(this.game.camera.scale).to({x:1,y:1},3000,"Linear",true);
                //animating the final fades
                this.curtainTween.to( { alpha: 1 }, 2000, "Linear", true,1000);
                this.curtainTween.onComplete.add(fadeToGameOver, this);
            }
            //this function will run the last animation
            function fadeToGameOver(){
                this.curtainTween.stop();
                this.exitCurtain.visible = true;
                this.exitCurtainTween.to( { alpha: 1 }, 3000, "Linear", true);
            }
        }
    }
    
};
