/**
 * The black and white parts of a snake eye, with constraints
 */
Eye = function(game, head, scale) {
    this.game          = game;
    this.head          = head;
    this.scale         = scale;
    this.eyeGroup      = this.game.add.group();
    this.collisionGroup= this.game.physics.p2.createCollisionGroup();
    this.debug         = false;

    //constraints that will hold the circles in place
    //the lock will hold the white circle on the head, and the distance
    //constraint (dist) will keep the black circle within the white one
    this.lock = null;
    this.dist = null;

    //initialize the circle sprites
    this.whiteCircle = this.game.add.sprite(
        this.head.body.x, this.head.body.y, "iris"
    );
    this.whiteCircle = this.initCircle(this.whiteCircle);

    this.blackCircle = this.game.add.sprite(
        this.whiteCircle.body.x, this.whiteCircle.body.y, "pupil"
    );
    this.blackCircle = this.initCircle(this.blackCircle);
    this.blackCircle.body.mass = 0.01;



}

Eye.prototype = {
    /**
     * Initialize a circle, whether it is the black or white one
     */
    initCircle: function(circle) {
        circle.scale.setTo(this.scale);
        this.game.physics.p2.enable(circle, this.debug);
        circle.body.clearShapes();
        //give the circle a circular physics body
        circle.body.addCircle(circle.width*0.5);
        circle.body.setCollisionGroup(this.collisionGroup);
        circle.body.collides([]);
        this.eyeGroup.add(circle);
        return circle;
    },
    /**
     * Ensure that the circles of the eye are constrained to the head
     * Array in the form [x,y] of offset from the snake head
     */
    updateConstraints: function(offset) {
        //change where the lock constraint of the white circle
        //is if it already exists
        if (this.lock) {
            this.lock.localOffsetB = [
                this.game.physics.p2.pxmi(offset[0]),
                this.game.physics.p2.pxmi(Math.abs(offset[1]))
            ];
        }
        //create a lock constraint if it doesn't already exist
        else {
            this.lock = this.game.physics.p2.createLockConstraint(
                this.whiteCircle.body, this.head.body, offset, 0
            );
        }

        //change the distance of the distance constraint for
        //the black circle if it exists already
        if (this.dist) {
            this.dist.distance = this.game.physics.p2.pxm(this.whiteCircle.width*0.25);
        }
        //create a distance constraint if it doesn't exist already
        else {
            this.dist = this.game.physics.p2.createDistanceConstraint(
                this.blackCircle.body, this.whiteCircle.body, this.whiteCircle.width*0.25
            );
        }
    },
    /**
     * Set the eye scale
     */
    setScale: function(scale) {
        this.scale = scale;
        for (var i = 0 ; i < this.eyeGroup.children.length ; i++) {
            var circle = this.eyeGroup.children[i];
            circle.scale.setTo(this.scale);
            //change the radii of the circle bodies using pure p2 physics
            circle.body.data.shapes[0].radius = this.game.physics.p2.pxm(circle.width*0.5);
        }

    },
    /**
     * Call from the update loop
     */
    update: function() {
        var mousePosX  = this.game.input.activePointer.worldX/this.game.camera.scale.x;
        var mousePosY  = this.game.input.activePointer.worldY/this.game.camera.scale.y;
        var headX      = this.head.body.x;
        var headY      = this.head.body.y;
        var mouseAngle = Math.atan2(mousePosY-headY, mousePosX-headX);
        var force      = 300;
        //direct the pupil toward the pointer

            this.blackCircle.body.moveRight(force*Math.cos(mouseAngle));
            this.blackCircle.body.moveDown(force*Math.sin(mouseAngle));
    },
    /**
     * Destroing function
     */
    destroy: function() {
        this.whiteCircle.destroy();
        this.blackCircle.destroy();
        this.game.physics.p2.removeConstraint(this.lock);
        this.game.physics.p2.removeConstraint(this.dist);
    }
};

/**
 * Creates a pair of eyes
 */
Eyes = function(game, head, scale) {
    this.game  = game;
    this.head  = head;
    this.scale = scale;
    this.eyes  = [];

    this.debug = false;

    //create two eyes
    var offset   = this.getOffset();
    this.leftEye = new Eye(this.game, this.head, this.scale);
    this.leftEye.updateConstraints([-offset.x, -offset.y]);
    this.eyes.push(this.leftEye);

    this.rightEye = new Eye(this.game, this.head, this.scale);
    this.rightEye.updateConstraints([offset.x, -offset.y]);
    this.eyes.push(this.rightEye);
}

Eyes.prototype = {
    /**
     * Get the offset that eyes should be from the head (based on scale)
     * offset distance with properties x and y
     */
    getOffset: function() {
        var xDim = this.head.width*0.25;
        var yDim = this.head.width*.125;
        return {x: xDim, y: yDim};
    },
    /**
     * Set the scale of the eyes
     */
    setScale: function(scale) {
        this.leftEye.setScale(scale);
        this.rightEye.setScale(scale);
        //update constraints to place them at the right offset
        var offset = this.getOffset();
        this.leftEye.updateConstraints([-offset.x, -offset.y]);
        this.rightEye.updateConstraints([offset.x, -offset.y]);
    },
    /**
     * Call from snake update loop
     */
    update: function() {
        for (var i = 0 ; i < this.eyes.length ; i++) {
            this.eyes[i].update();
        }
    },
    /**
     * Destroy this eye pair
     */
    destroy: function() {
        this.leftEye.destroy();
        this.rightEye.destroy();
    }
};
