/**
 * Shadow below the snake
 * first param is a game object
 * second is an Array of snake section sprites
 * lastly the scale of the shadow
 */
Shadow = function(game, modules, scale) {
    this.game         = game;
    this.modules      = modules;
    this.scale        = scale;

    this.shadowGroup  = this.game.add.group();
    this.shadows      = [];
    this.isLightingUp = false;


    this.lightStep    = 0;
    this.maxLightStep = 3;

    this.lightUpdCount= 0;
    this.updateLights = 3;

    //various tints that the shadow could have
    //since the image is white
    this.darkTint       = 0x000000;
    this.lightTintBright= 0x83FF83;
    this.lightTintDim   = 0xC6FFC6;
}

Shadow.prototype = {
    /**
     * Add a new shadow at a position coordinate coordinate
     */
    add: function(x, y) {
        var shadow = this.game.add.sprite(x, y, "shadow");
        shadow.scale.setTo(this.scale);
        shadow.anchor.set(0.5);
        this.shadowGroup.add(shadow);
        this.shadows.push(shadow);
    },
    /**
     * Call from the snake update loop
     */
    update: function() {
        let lastPos = null;
        for (var i = 0 ; i < this.modules.length ; i++) {
            var shadow = this.shadows[i];
            var pos    = {
                x: this.modules[i].body.x,
                y: this.modules[i].body.y
            };

            //hide the shadow if the previous shadow is in the same position
            if (lastPos && pos.x == lastPos.x && pos.y == lastPos.y) {
                shadow.alpha        = 0;
                shadow.naturalAlpha = 0;
            }
            else {
                shadow.alpha        = 1;
                shadow.naturalAlpha = 1;
            }
            //place each shadow below a snake section
            shadow.position.x = pos.x;
            shadow.position.y = pos.y;

            lastPos = pos;
        }

        //light up shadow with bright tints
        if (this.isLightingUp) {
            this.lightUpdCount++;
            if (this.lightUpdCount >= this.updateLights) {
                this.lightUp();
            }
        }
        //make shadow dark
        else {
            for (var i = 0 ; i < this.shadows.length ; i++) {
                var shadow  = this.shadows[i];
                shadow.tint = this.darkTint;
            }
        }
    },
    /**
     * Set scale of the shadow
     */
    setScale: function(scale) {
        this.scale = scale;
        for (var i = 0 ; i < this.shadows.length ; i++) {
            this.shadows[i].scale.setTo(scale);
        }
    },
    /**
     * Light up the shadow from a gray to a bright color
     */
    lightUp: function() {
        this.lightUpdCount = 0;
        for (var i = 0 ; i < this.shadows.length ; i++) {
            var shadow = this.shadows[i];
            if (shadow.naturalAlpha > 0) {
                //create an alternating effect so shadow is not uniform
                if ((i - this.lightStep) % this.maxLightStep === 0 ) {
                    shadow.tint = this.lightTintBright;
                }
                else {
                    shadow.tint = this.lightTintDim;
                }
            }
        }
        //use a counter to decide how to alternate shadow tints
        this.lightStep++;
        if (this.lightStep == this.maxLightStep) {
            this.lightStep = 0;
        }
    },
    /**
     * destroy the shadow
     */
    destroy: function() {
        for (var i = this.shadows.length - 1 ; i >= 0 ; i--) {
            this.shadows[i].destroy();
        }
    }
};




// ------------------------------------ Snake Object ---------------------------------------
Snake = function(game, spriteKey, x, y) {
    this.game = game;
    //create an array of snakes in the game object and add this snake
    if (!this.game.snakes) {
        this.game.snakes = [];
    }
    this.game.snakes.push(this);
    this.debug         = false;
    this.snakeLength   = 0;
    this.initialLength = 5;
    this.spriteKey     = spriteKey;

    //various quantities that can be changed
    var increase       = this.game.levels - 1;
    var acceleration   = 40;
    this.scale         = 0.5;
    this.fastSpeed     = 230+increase*acceleration;
    this.normalSpeed   = 125+increase*acceleration;
    this.slowSpeed     = 63+increase*acceleration;
    this.speed         = this.normalSpeed;
    this.rotationSpeed = 55;

    //initialize groups and arrays
    this.collisionGroup = this.game.physics.p2.createCollisionGroup();
    this.modules        = [];
    //the head path is an array of points that the head of the snake has
    //traveled through
    this.headPath = [];
    this.food     = [];

    this.preferredDistance = 15 * this.scale;
    this.queuedmodules    = 0;

    //initialize the shadow
    this.shadow       = new Shadow(this.game, this.modules, this.scale);
    this.sectionGroup = this.game.add.group();
    //add the head of the snake
    this.head         = this.addSectionAtPosition(x,y);
    this.head.name    = "head";
    this.head.snake   = this;

    this.lastHeadPosition = new Phaser.Point(this.head.body.x, this.head.body.y);
    //add 5 modules behind the head
    this.initmodules(this.initialLength);

    //initialize the eyes
    this.eyes = new Eyes(this.game, this.head, this.scale);

    //the edge is the front body that can collide with other snakes
    //it is locked to the head of this snake
    this.edgeOffset = 4;
    this.edge       = this.game.add.sprite(x, y - this.edgeOffset, this.spriteKey);
    this.edge.name  = "edge";
    this.edge.alpha = 0;
    this.game.physics.p2.enable(this.edge, this.debug);
    this.edge.body.setCircle(this.edgeOffset);

    //constrain edge to the front of the head
    this.edgeLock = this.game.physics.p2.createLockConstraint(
        this.edge.body, this.head.body, [0, -this.head.width*0.5-this.edgeOffset]
    );

    this.edge.body.onBeginContact.add(this.edgeContact, this);

    this.onDestroyedCallbacks = [];
    this.onDestroyedContexts  = [];
}

Snake.prototype = {
    /**
     * Give the snake starting segments
     *num is the number of snake modules to be created
     */
    initmodules: function(num) {
        //create a certain number of modules behind the head
        //only use this once
        for (var i = 1 ; i <= num ; i++) {
            var x = this.head.body.x;
            var y = this.head.body.y + i * this.preferredDistance;
            this.addSectionAtPosition(x, y);
            //add a point to the head path so that the section stays there
            this.headPath.push(new Phaser.Point(x,y));
        }

    },

     //Add a section to the snake at a given position
    addSectionAtPosition: function(x, y) {
        //initialize a new section
        var sec = this.game.add.sprite(x, y, this.spriteKey);
        this.game.physics.p2.enable(sec, this.debug);
        sec.body.setCollisionGroup(this.collisionGroup);
        sec.body.collides([]);
        sec.body.kinematic = true;

        this.snakeLength++;
        this.sectionGroup.add(sec);
        sec.sendToBack();
        sec.scale.setTo(this.scale);

        this.modules.push(sec);

        this.shadow.add(x,y);
        //add a circle body to this section
        sec.body.clearShapes();
        sec.body.addCircle(sec.width*0.5);

        return sec;
    },
    /**
     * Add to the queue of new modules
     * Number of modules to add to queue
     */
    addmodulesAfterLast: function(amount) {
        this.queuedmodules += amount;
    },
    
     //Call from the main update loop
    update: function() {
        var speed = this.speed;
        this.head.body.moveForward(speed);

      /*remove the last element of an array that contains points which
        the head traveled through
        then move this point to the front of the array and change its value
        to be where the head is located */
        var point = this.headPath.pop();
        point.setTo(this.head.body.x, this.head.body.y);
        this.headPath.unshift(point);

        //place each section of the snake on the path of the snake head,
        //a certain distance from the section before it
        var index = 0;
        var lastIndex = null;
        for (var i = 0 ; i < this.snakeLength ; i++) {

            this.modules[i].body.x = this.headPath[index].x;
            this.modules[i].body.y = this.headPath[index].y;

            //hide modules if they are at the same position
            if (lastIndex && index == lastIndex) {
                this.modules[i].alpha = 0;
            }
            else {
                this.modules[i].alpha = 1;
            }

            lastIndex = index;
            //this finds the index in the head path array that the next point
            //should be at
            index = this.findNextPointIndex(index);
        }

      /*continuously adjust the size of the head path array so to
        keep only an array of points needed */
        if (index >= this.headPath.length - 1) {
            var lastPos = this.headPath[this.headPath.length - 1];
            this.headPath.push(new Phaser.Point(lastPos.x, lastPos.y));
        }
        else {
            this.headPath.pop();
        }

      /*this calls onCycleComplete every time a cycle is completed
        a cycle is the time it takes the second section of a snake to reach
        where the head of the snake was at the end of the last cycle */
        var i = 0;
        var found = false;
        while (this.headPath[i].x != this.modules[1].body.x &&
        this.headPath[i].y != this.modules[1].body.y) {
            if (this.headPath[i].x == this.lastHeadPosition.x &&
            this.headPath[i].y == this.lastHeadPosition.y) {
                found = true;
                break;
            }
            i++;
        }
        if (!found) {
            this.lastHeadPosition = new Phaser.Point(this.head.body.x, this.head.body.y);
            this.onCycleComplete();
        }

        //update the eyes and the shadow below the snake
        this.eyes.update();
        this.shadow.update();
    },
    /**
     * Find in the headPath array which point the next section of the snake
     * should be placed at, based on the distance between points
     */
    findNextPointIndex: function(currentIndex) {
        var pt = this.headPath[currentIndex];
      /*here i am trying to find a point at approximately this distance away
        from the point before it, where the distance is the total length of
        all the lines connecting the two points */
        var prefDist = this.preferredDistance;
        var len      = 0;
        var dif      = len - prefDist;
        var i        = currentIndex;
        var prevDif  = null;
      /*this loop sums the distances between points on the path of the head
        starting from the given index of the function and continues until
        this sum nears the preferred distance between two snake modules */
        while (i+1 < this.headPath.length && (dif === null || dif < 0)) {
            //get distance between next two points
            var dist = Util.distanceFormula(
                this.headPath[i].x, this.headPath[i].y,
                this.headPath[i+1].x, this.headPath[i+1].y
            );
            len += dist;
            prevDif = dif;
            //i am trying to get the difference between the current sum and
            //the preferred distance close to zero
            dif = len - prefDist;
            i++;
        }

      /*choose the index that makes the difference closer to zero
        once the loop is complete */
        if (prevDif === null || Math.abs(prevDif) > Math.abs(dif)) {
            return i;
        }
        else {
            return i-1;
        }
    },
  /*Called each time the snake's second section reaches where the
    first section was at the last call (completed a single cycle)*/
    onCycleComplete: function() {
        if (this.queuedmodules > 0) {
            var lastSec = this.modules[this.modules.length - 1];
            this.addSectionAtPosition(lastSec.body.x, lastSec.body.y);
            this.queuedmodules--;
        }
    },

    //Set snake scale
    setScale: function(scale) {
        this.scale             = scale;
        this.preferredDistance = 17 * this.scale;

        //update edge lock location with p2 physics
        this.edgeLock.localOffsetB = [
            0, this.game.physics.p2.pxmi(this.head.width*0.5+this.edgeOffset)
        ];

        //scale modules and their bodies
        for (var i = 0 ; i < this.modules.length ; i++) {
            var sec = this.modules[i];
            sec.scale.setTo(this.scale);
            sec.body.data.shapes[0].radius = this.game.physics.p2.pxm(sec.width*0.5);
        }

        //scale eyes and shadows
        this.eyes.setScale(scale);
        this.shadow.setScale(scale);
    },
    
    //Increment length and scale
    incrementSize: function() {
        this.addmodulesAfterLast(1);
        this.setScale(this.scale * 1.01);
    },

    //Destroy the snake
    destroy: function() {
        this.game.snakes.splice(this.game.snakes.indexOf(this), 1);
        //remove constraints
        this.game.physics.p2.removeConstraint(this.edgeLock);
        this.edge.destroy();
        //destroy food that is constrained to the snake head
        for (var i = this.food.length - 1 ; i >= 0 ; i--) {
            this.food[i].destroy();
        }
        //destroy everything else
        this.modules.forEach(function(sec, index) {
            sec.destroy();
        });
        this.eyes.destroy();
        this.shadow.destroy();

        //call this snake's destruction callbacks
        for (var i = 0 ; i < this.onDestroyedCallbacks.length ; i++) {
            if (typeof this.onDestroyedCallbacks[i] == "function") {
                this.onDestroyedCallbacks[i].apply(
                    this.onDestroyedContexts[i], [this]);
            }
        }
    },

    // Called when the front of the snake (the edge) hits something
    edgeContact: function(phaserBody) {
        //if the edge hits this snake section or the world borders, destroy this snake
            if(this.snakeLength > this.initialLength+1) {
                this.destroy();
            }else if (phaserBody && this.modules.indexOf(phaserBody.sprite) == -1) {
                this.destroy();
            }else if (phaserBody) {
                this.edge.body.x = this.head.body.x;
                this.edge.body.y = this.head.body.y;
            }
    },

    // Add callback for when snake is destroyed
    addDestroyedCallback: function(callback, context) {
        this.onDestroyedCallbacks.push(callback);
        this.onDestroyedContexts.push(context);
    }
};