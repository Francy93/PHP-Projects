/**
 * Food that snakes eat - it is pulled towards the center of a snake head after
 * it is first touched
 */
Food = function(game, x, y) {
    this.game   = game;
    this.debug  = false;
    this.sprite = this.game.add.sprite(x, y, 'particle');
    //theses lines below allow food to appear gradualy by fading up within 1 second
    this.sprite.alpha = 0;

    var delay  = 1000;
    var effect = "Linear";
    //particle doesn't bounce at first start
    if(this.game.camera.scaled && this.game.end == 0){ //condition satisfied after start and before end
        delay  = 200;
        effect = Phaser.Easing.Bounce.Out;
        this.game.add.tween(this.sprite.anchor).to( {y:[2, 0.5] }, 1000, effect, true,delay);
    }
    //if Snake dies then disply replacement food immediately
    if(this.game.end > 0){delay = 0; effect = Phaser.Easing.Bounce.Out;}
    this.game.add.tween(this.sprite).to( { alpha: 1 }, 1000, effect, true,delay);
    this.game.add.tween(this.sprite.scale).to( {x: [0, 1], y:[0, 1] }, 1000, effect, true,delay);

    //array of ascii code colours
    var colors = [0xff0000, 0x0000ff, 0x00ff00, 0xffff00, 0XFFFFFF, 0X9900FF, 0Xffa500];
    //pinting the sprite (food) with a random color
    this.sprite.tint = colors[Math.floor(Math.random() * 7)];

    this.game.physics.p2.enable(this.sprite, this.debug);
    this.sprite.body.clearShapes();
    //setting the range of food attraction to the snake
    this.sprite.body.addCircle(this.sprite.width * 0.4);
    //set callback for when something hits the food
    this.sprite.body.onBeginContact.add(this.onBeginContact, this); 
    this.sprite.food = this;
    this.head        = null;
    this.constraint  = null;
}

Food.prototype = {
    //this determines the food movement
    foodMovement: function(particle) {
        //this variable get each time a random number of a specified range
        var rand = this.game.rnd.realInRange(0.60, 1.30)
        if(rand <= particle.scale.x + 0.1 && rand >= particle.scale.x - 0.1){
            particle.scale.x = rand;//here particle scale.X will be set
        }
        if(rand <= particle.scale.y + 0.1 && rand >= particle.scale.y - 0.1){
            particle.scale.y = rand;//here particle scale.Y will be set
        }
        //this if conditions determie the intensity of the particle shake (movement)
        var intensity = 2;
        //generating a random number (positive and negative) within a range
        var direction = Util.randomFloat(12,17, this.game);
        if((direction <= particle.body.velocity.x + intensity &&
            direction >= particle.body.velocity.x - intensity) ||
            particle.body.velocity.x == 0){
                particle.body.velocity.x = direction*-1;//here particle velocity.X will be set
        }
        direction = Util.randomFloat(12,17, this.game);
        if((direction <= particle.body.velocity.y + intensity && 
            direction >= particle.body.velocity.y - intensity) ||
            particle.body.velocity.y == 0){
                particle.body.velocity.y = direction*-1;//here particle velocity.Y will be set
        }
    },

    onBeginContact: function(phaserBody) {
        if (phaserBody && phaserBody.sprite.name == "head" && this.constraint === null) {
            this.sprite.body.collides([]);
            //Create constraint between the food and the snake head that
            //it collided with. The food is then brought to the center of
            //the head sprite
            this.constraint = this.game.physics.p2.createRevoluteConstraint(
                this.sprite.body, [0,0], phaserBody, [0,0]
            );
            this.head = phaserBody.sprite;
            this.head.snake.food.push(this);
        }
    },
    /**
     * Call from main update loop
     */
    update: function() {
        //this boolean determines if a new particle will be released (called from GameCode.update)
        var eaten = false;
        this.foodMovement(this.sprite);
        //once the food reaches the center of the snake head, destroy it and
        //increment the size of the snake
        if (this.head && Math.round(this.head.body.x) == Math.round(this.sprite.body.x) &&
        Math.round(this.head.body.y) == Math.round(this.sprite.body.y)) {
            this.head.snake.incrementSize();
            this.destroy();
            eaten = true;
        }
        return eaten;
    },
    /**
     * Destroy this food and its constraints
     */
    destroy: function() {
        if (this.head) {
            this.game.physics.p2.removeConstraint(this.constraint);
            this.sprite.destroy();
            this.head.snake.food.splice(this.head.snake.food.indexOf(this), 1);
            this.head = null;
        }
    }
};
