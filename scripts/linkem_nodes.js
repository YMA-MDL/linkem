/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ajaxUrl = "ajax/Ajax.php";

/*
 * Node Class
 */

function Node() {

    var that = this;

    this.properties = {};
    // initialize
    this.createInDb = function () {
        $.post(ajaxUrl, {
            action: "node_add",
            type: that.type,
            uniqueId: that.id
        }).success(function (data) {
            // when success add the node to the graph
            // create the node object
            // add it to the graph
            console.log(data);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.createId = function () {
        that.id = guid();
    };
    
    this.retrieveId = function(){
        that.id = that.properties.uniqueId;
    };
    

    this.setType = function (nodeType) {
        that.type = nodeType;
    };

    this.addToGraph = function (graph) {
        graph.graphAddNode(that.type, that.id);
    };

    this.updateNodeMetaData = function () {
    };

    this.updateProperty = function (propName, propValue) {
        $.post(ajaxUrl, {
            action: "node_update",
            uniqueId: that.id,
            propName: propName,
            propValue: propValue
        }).success(function (data) {
            // when success delete the node from the graph
            that.properties[propName] = propValue;
        }).fail(function (err) {
            console.log(err);
        });
        
    };

    this.deleteNode = function () {
        $.post(ajaxUrl, {
            action: "node_delete",
            uniqueId: that.id
        }).success(function (data) {
            // when success delete the node from the graph
        }).fail(function (err) {
            console.log(err);
        });
    };
}


