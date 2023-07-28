'use strict'

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////// Last State //////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////

var End = {
    preload: function(){
        //loading images
        this.game.load.image('fullScreen' , 'Assets/Game/fullScreen.png');
        this.game.load.image('again'      , 'Assets/Game/again.png');
        this.game.load.image('save'       , 'Assets/Game/SaveScore.png');
        this.game.load.image('background' , 'Assets/Game/GameOverBG.png');
        this.game.load.image('blur'       , 'Assets/Game/backBlur.png');
    },
    create: function(){
        var width  = this.game.width;   // variable game width
        var height = this.game.height; // variable game height

        //setting the world scene size
        this.game.world.setBounds(0, 0, width, height);
        this.game.stage.backgroundColor = '#444';
        this.game.camera.scale.setTo(1,1); //initial scale has to be standard

        //adding the sprite background
        var bg = this.game.add.sprite(width/2,height/2, 'background');
        bg.anchor.setTo(0.5, 0.5);
        /* bg.x = width/2;
        bg.y = height/2; */
        bg.width = width;
        bg.height= height;
        bg.fixedToCamera = true;
        
        //the score labels
        var style = { font: "25px Arial", fill: "#ffffff", align: "center", stroke: "#000000", strokeThickness: 2};
        this.style = style;
        this.score = this.game.fianlScore;
        this.labelScore = this.game.add.text(width/2,height/2, "Totalized: "+this.score, style);
        this.labelScore.anchor.setTo(0.5, 0.5);
        this.labelScore.alpha = 0;
        this.game.add.tween(this.labelScore).to({alpha: 1},3000,Phaser.Easing.Quintic.InOut,true,2000);
        this.labelScore.score = 0;
        this.game.add.tween(this.labelScore).to({score: this.score},4000,Phaser.Easing.Quintic.InOut,true,5000);
        //the level label
        this.level = this.game.finalLevel;
        this.labelLevel = this.game.add.text(width/2,0, "Level reached: "+this.level, style);
        this.labelLevel.y = height/2+this.labelLevel.height;
        this.labelLevel.anchor.x =  0.5;
        this.labelLevel.scale.setTo(0.6,0.6);
        this.labelLevel.alpha = 0;
        this.game.add.tween(this.labelLevel).to({alpha: 1},1000,Phaser.Easing.Quintic.InOut,true,1000);

        //adding "Play" and "save" button and actions
        this.save = this.game.add.button(0,0, 'save', this.goAccount, this);
        this.play = this.game.add.button(0,0, 'again', this.restart, this);

        //button setyle and settings
        toTheCenter(this.save, this.game);
        toTheCenter(this.play, this.game);
        function toTheCenter(button, game){
            //sprite.anchor.setTo(0.5, 0.5);
            button.fixedToCamera = false;
            button.width         = width/100*7;
            button.height        = button.width;
            button.y             = height/3*2 + button.height/2;


            if(button.key == "save"){
                button.tint = 0xffff00;  //yellow color
                button.x    = width - button.width*2; //- this.button.width/2;
                //configutin the "save" button animation
                button.sElevate      = game.add.tween(button); //when over
                var newY             = button.y - 10;
                button.sElevateParam = button.sElevate.to({y:newY},1000,Phaser.Easing.Elastic.Out,false);
                button.sFall         = game.add.tween(button); //when out
                var origY            = button.y;
                button.sFallParamL   = button.sFall.to({y:origY},1000,Phaser.Easing.Bounce.Out,false);
            }else{
                button.tint = 0x00ff00; //green color
                button.x    = width/2 - button.width*2;
                //configutin the "play" button animation
                button.sRotateL       = game.add.tween(button); //when over
                button.sRotateParamL  = button.sRotateL.to({rotation:-5},1000,Phaser.Easing.Back.Out,false);
                button.sRotateR       = game.add.tween(button); //when out
                button.sRotateParamR  = button.sRotateR.to({rotation: 5},1000,Phaser.Easing.Back.Out,false);
                button.sRotateGo      = game.add.tween(button); //when up 1-2
                button.sRotateParamGo = button.sRotateGo.to({rotation:-100},3000,Phaser.Easing.Quintic.InOut,false);
            }
            button.sOver = false;
            //save and play animation
            button.sScale      = game.add.tween(button.scale); //when up 2-2
            var configP        = {x:[0.2,button.scale.x],y:[0.2,button.scale.x]};
            button.sScaleParam = button.sScale.to(configP,1000,Phaser.Easing.Bounce.Out,false);

            button.firstPos = {x:button.x, y:button.y} // creating an object inside sprite with initial positions
            button.sMove = game.add.tween(button); // creating an object inside sprite with this movement target
            button.anchor.setTo(0.5,0.5);
            //sprite.scale.setTo(0.05,0.05);
            //sprite.fixedToCamera = true;
        }
        
        //adding "Play" and "save" text
        var style = { font: "25px Arial", fill: "#ffffff", align: "center", stroke: "#000000", strokeThickness: 2};
        this.labelSave = this.game.add.text(0, 0, "Log-In to save", style);
        this.labelPlay = this.game.add.text(0, 0, "Play again?", style);

        this.labelSave.width      = width/100*25;
        this.labelSave.heigth     = height/100*10;
        this.labelPlay.width      = width/100*25;
        this.labelPlay.heigth     = height/100*10;
        this.labelSave.centerY    = this.save.centerY;
        this.labelPlay.centerY    = this.play.centerY;
        this.labelSave.position.x = this.save.x - this.labelSave.width - 10 - this.save.width/2;
        this.labelPlay.position.x = this.play.x - this.labelPlay.width - 10 - this.play.width/2;
        //this.labelPlay.scale.setTo(0.6, 0.6);

        //getting al the global setting
        this.global = new GlobalCreate(this.game, width,height);
        //hiding the superflous
        this.global.labelScore.visible  = false;
        this.global.levelNumber.visible = false;

        //starting the button animation
        //play button trigger
        this.play.onInputOver.add(this.over, this);
        this.play.onInputOut.add(this.out, this);
        this.play.onInputUp.add(this.up, this);
        //save button trigger
        this.save.onInputOver.add(this.over, this);
        this.save.onInputOut.add(this.out, this);
        this.save.onInputUp.add(this.up, this);
        //if user is logged, don't show the save button
        if ('user' in sessionStorage){
            this.save.visible = false;
            this.labelSave.visible = false;
        }
        //saving the score into the sessionStorage or localStorage
        this.scoreSaving();
    },

    //-----------------------------------------
    //------------------MAIN-------------------
    //-----------------------------------------
    update: function(){
        //displaay the increasing score number
        this.labelScore.text = "Your score: "+ this.labelScore.score.toFixed(2);

        //button animation
        this.bMove(this.save);
        this.bMove(this.play);
    },



    //---------------------------------BUTTON EVENT ANIMATIONS--------------------------------

      //making the buttons flutuate as they where particles
      bMove: function (button){
        if(!button.sMove.isRunning && !button.sOver){
            var fX= button.firstPos.x;
            var fY= button.firstPos.y;
            button.sMove = this.game.add.tween(button);
            var config={x:Math.abs(Util.randomFloat(fX-2,fX+2,this.game)),
                        y:Math.abs(Util.randomFloat(fY-2,fY+2,this.game))};
            button.sMovement = button.sMove.to(config,200,"Linear",false,0);
            
            button.sMove.start();
        }
    },
    //animation pointer over button
    over: function(button){
        if(button == this.play && !button.sRotateL.isRunning && !button.sRotateGo.isRunning){
            //button play animation if over play button
            button.sRotateL.start();
        }else if(button === this.save && !button.sElevate.isRunning){
            //button save animation if over
            button.sMove.stop();
            button.sFall.stop();
            button.sElevate = this.game.add.tween(button);
            button.sElevateParam = button.sElevate.to({y:button.firstPos.y-10},1000,Phaser.Easing.Elastic.Out,true);
            button.sOver = true;
        }
    },
    //animation pointer out button
    out: function(button){
        if(button == this.play && !button.sRotateR.isRunning && !button.sRotateGo.isRunning){
            //button play animation if out play button
            button.sRotateR.start();
        }else if(button === this.save && !button.sFall.isRunning){
            //button save animation if out
            button.sMove.stop();
            button.sElevate.stop();
            button.sFall = this.game.add.tween(button);
            button.sFallParamL = button.sFall.to({y:button.firstPos.y},1000,Phaser.Easing.Bounce.Out,true);
            button.sFall.onComplete.add(()=>{button.sOver = false; }, this);
        }
    },
    //animation pointer up button
    up: function(button){
        if(button == this.play && !button.sScale.isRunning && !button.sRotateGo.isRunning){
            //button play animation if up play button
            button.sScale.start();
            button.sRotateGo.start();
        }else if(button === this.save && !button.sScale.isRunning){
            //button save animation if up
            button.sScale.start();
        }
    },

    //---------------------------------BUTTON EVENT ACTIONS--------------------------------
    restart: function(){
        this.global.curtain.visible = true;
        this.global.curtain.alpha = 0;
        var curtainTween = this.game.add.tween(this.global.curtain).to( { alpha: 1}, 1000, "Linear", true, 1000);
        //as the animation terminate start the game state
        curtainTween.onComplete.add(()=>{this.game.state.start('TheGame')}, this);
    },

    //changing page by redirecting to Account page
    goAccount: function(){
        window.location.href = "account.php";
    },

    //storing the score into the browsware storage
    scoreSaving: function(){
        let score = this.game.fianlScore == undefined? 0: this.game.fianlScore;
        let today = new Date().toLocaleString();

        if ('user' in sessionStorage){
            let user = JSON.parse(sessionStorage.user).eMail;//getting the user id
            let data = JSON.parse(localStorage[user]); //getting the user data object
            if (!("score" in data)){ //if the score key does not exist yet make one
                let objScore ={} //defining the temp obj
                objScore[today] = score; //init the temp obj with just new data
                data.score = objScore; //adding the new data (score and date)
            }else{ data.score[today] = score}
            
            data.score = sorting(data.score); //sorting sorted score data
            localStorage.setItem(user, JSON.stringify(data)); //updating data back to the localStorage as JSON
        }else{
            if("tempScore" in sessionStorage){ //if tempScore exists already
                let data = JSON.parse(sessionStorage.tempScore); //getting the data
                data[today] = score; //updating the data
                sessionStorage.setItem("tempScore", JSON.stringify(sorting(data))); //storing the updated data
            }else{
                //if tempScore did not exists yet
                let newData ={};
                newData[today] = score; 
                sessionStorage.setItem("tempScore", JSON.stringify(newData));
            }
        }
        //function to sort data
        function sorting(data){
            //this is the sorting algorithm which sort the second column data in a decreasing way
            const sortedData = Object.fromEntries(Object.entries(data).sort(([,a],[,b]) => b-a));
            return sortedData;
        }
    }
}

