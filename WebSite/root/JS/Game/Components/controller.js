/**
 * Getting inputs to controll the Snake
 */
PlayerSnake = function(game, spriteKey, x, y) {
    Snake.call(this, game, spriteKey, x, y);
    this.cursors     = game.input.keyboard.createCursorKeys();
    this.ignoreMouse = false;
    this.player      = true;
    this.alive       = true;

    //Setting buttons keys and mouse controller
    var click     = this.game.input.activePointer.leftButton;
    var spaceKey  = this.game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);
    var arrowUp   = this.game.input.keyboard.addKey(Phaser.Keyboard.UP);
    var arrowDown = this.game.input.keyboard.addKey(Phaser.Keyboard.DOWN);
    var self      = this;
    click.onDown.add(self.speedMax,      self);      //when the mouseClick is pressed
    click.onUp.add(self.speedNormal,     self);      //when the mouseClick is released
    spaceKey.onDown.add(self.speedMax,   self);      //when the spaceBar is pressed
    spaceKey.onUp.add(self.speedNormal,  self);      //when the spaceBar is released
    arrowUp.onDown.add(self.speedMax,    self);      //when the arrowUp key is pressed
    arrowUp.onUp.add(self.speedNormal,   self);      //when the arrowUp key is released
    arrowDown.onDown.add(self.speedSlow, self);      //when the arrowDown key is pressed
    arrowDown.onUp.add(self.speedNormal, self);      //when the arrowDown key is released
    self.addDestroyedCallback(function() {
        click.onDown.remove(self.speedMax,      self);           
        click.onUp.remove(self.speedNormal,     self);          
        spaceKey.onDown.remove(self.speedMax,   self);
        spaceKey.onUp.remove(self.speedNormal,  self);
        arrowUp.onDown.remove(self.speedMax,    self);
        arrowUp.onUp.remove(self.speedNormal,   self);
        arrowDown.onDown.remove(self.speedSlow, self);
        arrowDown.onUp.remove(self.speedNormal, self);
    }, self);
}

PlayerSnake.prototype = Object.create(Snake.prototype);
PlayerSnake.prototype.constructor = PlayerSnake;

//make this snake light up and speed up when the space key is down
PlayerSnake.prototype.speedMax = function() {
    this.speed = this.fastSpeed;
    this.shadow.isLightingUp = true;
}
//make the snake speed get normal when the space key is up again
PlayerSnake.prototype.speedNormal = function() {
    this.speed = this.normalSpeed;
    this.shadow.isLightingUp = false;
}
//make the snake slow down when the space key is up again
PlayerSnake.prototype.speedSlow = function() {
    this.speed = this.slowSpeed;
    this.shadow.isLightingUp = false;
}

/**
 * Add functionality to the original snake update method so that the player
 * can control where this snake goes
 */
PlayerSnake.prototype.tempUpdate = PlayerSnake.prototype.update;
PlayerSnake.prototype.update = function() {
    //find the angle that the head needs to rotate
    //through in order to face the mouse
    var mousePosX = this.game.input.activePointer.worldX/this.game.camera.scale.x;
    var mousePosY = this.game.input.activePointer.worldY/this.game.camera.scale.y;
    var headX     = this.head.body.x;
    var headY     = this.head.body.y;
    var angle     = (180*Math.atan2(mousePosX-headX, mousePosY-headY)/Math.PI);
    
    if (this.ignoreMouse) { //checking if the mouse moves and if so set "ignoreMouse" to false
        this.game.input.addMoveCallback(()=>this.ignoreMouse = false);
    }
    if (angle > 0) {  angle = 180-angle; }
    else { angle = -180-angle; }
    
    switch (true){         //allow arrow keys to be used
        case    this.cursors.left.isUp && this.cursors.right.isUp ||
                this.cursors.left.isDown && this.cursors.right.isDown:
            //this ternary operator here below determines if the snake
            //follows the last given input from mouse or arrowkeys
            var dif = !this.ignoreMouse ? this.head.body.angle - angle : dif; 
            this.head.body.setZeroRotation();
            break;
        case this.cursors.left.isDown:
            this.head.body.rotateLeft(this.rotationSpeed);
            this.ignoreMouse = true;
            break;
        case this.cursors.right.isDown:
            this.head.body.rotateRight(this.rotationSpeed);
            this.ignoreMouse = true;
            break;
    }
    //decide whether rotating left or right will angle the head towards
    //the mouse faster, if arrow keys are not used
    if (dif < 0 && dif > -180 || dif > 180) {
        this.head.body.rotateRight(this.rotationSpeed);
    }
    if (dif > 0 && dif < 180 || dif < -180) {
        this.head.body.rotateLeft(this.rotationSpeed);
    }

    //call the original snake update method
    this.tempUpdate();
}
