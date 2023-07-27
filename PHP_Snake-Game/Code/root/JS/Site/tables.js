'use strict';

/**
* setting the 2 tables variables
*/
function setTabVar(){
    const accountTable = document.querySelector('#accountTable');
    const rankingTable = document.querySelector('#rankingTable');
    const tab          = accountTable ? accountTable: rankingTable;
    const sort         = accountTable ? 0 : 1;
    const filler       = new Table(tab, sort);
    filler.tableFiller();
}


class Table {
    /**
     * Parameter expected from the class Table
     * @constructor
     * @param  {Object} tab DOM element (table)
     * @param  {Number} sort kind of table sorting
     */
    constructor(tab, sort) {
        this.tab  = tab;
        this.sort = sort;
    }
    
    /**
    * filling operation
    */
    tableFiller(sort){
        this.sort      = sort? sort:this.sort;
        this.table     = this.tab.firstElementChild;
        this.cycles    = this.table.rows.length -1;
        this.usersData = new Object;
        this.sorted    = false;

        //establishing the max amount of table rows (ciles) 
        if (this.sort === 0){
            const user    = JSON.parse(sessionStorage.user).eMail;
            this.userData = JSON.parse(localStorage[user]);
            let newCycles = Object.keys(this.userData.score).length;
            this.cycles   = newCycles<this.cycles? newCycles:this.cycles;
            this.#tableLoop();
        }else if(this.sort === 1){
            let newCycles  = Object.keys(localStorage).length;
            this.cycles    = newCycles<this.cycles? newCycles:this.cycles;
            this.#tableLoop();
            this.usersData = this.sorting2(this.usersData);
            this.sorted    = true;
            this.#tableLoop();
        }
    }
    //cicling the table
    #tableLoop (){
        for (let i=0;i<this.cycles; i++){
            let row = this.table.rows[i+1];
            for (let j=0;j<row.cells.length; j++){
                
                let cell = row.cells[j];
                this.#setTables(cell, i, j);
            }        
        }
    }

    /**
     * function to set cell data
     * @param  {Object} cell DOM element (cell)
     * @param  {Number} row row index i
     * @param  {Number} column column index j
     */   
    #setTables(cell, row, column){
        if(this.sort === 0){
            const user     = JSON.parse(sessionStorage.user).eMail;
            const userData = JSON.parse(localStorage[user]);
            const scoreData= Object.entries(userData.score)[row];
            const date     = scoreData[0].split(',')[0];
            const time     = scoreData[0].split(',')[1];
            const score    = scoreData[1];
            //putting the values into Account table cells
            switch(column){
                case 0:
                    cell.innerHTML = date;
                    break;
                case 1:
                    cell.innerHTML = time;
                    break;
                case 2:
                    cell.innerHTML = score;
                    break;
                default: console.log("column not contemplated");
            }
        }else if(!this.sorted){
            const user     = localStorage.key(row);
            const userData = JSON.parse(localStorage[user]);
            const date     =  Object.keys(userData.score)[0].split(',')[0];
            let userObj    = new Object;
            userObj[date]  = Object.values(userData.score)[0];
            this.usersData[userData.Nikname] = userObj;
        }else{
            const nikname = Object.keys(this.usersData)[row];
            const date    = Object.keys(this.usersData[nikname])[0];
            const score   = Object.values(this.usersData[nikname])[0];
            //putting the values into Ranking table cells
            switch(column){
                case 0:
                    cell.innerHTML = nikname;
                    break;
                case 1:
                    cell.innerHTML = date;
                    break;
                case 2:
                    cell.innerHTML = score;
                    break;
                default: console.log("column not contemplated");
            }
        }
    }
    /**
    * the sorting algorithm
    * @param  {Object} data unsorted score data
    * @return {Object} returns the sorted Object
    */
    sorting2(data){
        //this is the sorting algorithm which sort the second column data in a decreasing way
        return Object.fromEntries(Object.entries(data).sort(([,a],[,b]) => Object.values(b)[0]-Object.values(a)[0]));
    }
}