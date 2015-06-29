/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ajaxUrl = "ajax/Ajax.php";

/*
 * Node Class
 */

function Node(nodeType) {

    // properties

    var that = this;

    this.properties = {};

    this.label = "";

    this.type = nodeType;

    // functions

    this.setLabel = function(){
        var nodeTemplate = nodeTemplateList.getTemplateByName(that.type);
        if (nodeTemplate.label !== undefined){
            var labels = nodeTemplate.label.split(',');
            labelValueArray = [];
            for (var i = 0; i < labels.length; i++) {
                if (that.properties[labels[i]] != undefined){
                labelValueArray.push(that.properties[labels[i]]);
                }else {
                    labelValueArray.push ("");
                }
            }
            that.label = labelValueArray.join("-");
        }
    };


    this.createInDb = function () {
        $.post(ajaxUrl, {
            action: "node_add",
            type: that.type,
            uniqueId: that.id
        }).success(function (data) {
            // when success add the node to the graph
            // create the node object
            // add it to the graph

        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updateTemplateProperties = function(){
        for (var i = 0; i < nodeTemplateList.nodeTemplates.length; i++) {
            if (nodeTemplateList.nodeTemplates[i].name === that.type){
                for (var key in  nodeTemplateList.nodeTemplates[i].properties) {
                    if (!(that.properties.hasOwnProperty(key))&&(!key.startsWith("_"))&&(key !== 'name')&&(key !== 'image')){
                        that.properties[key] = '';
                    }
                }
            }
        }
    };

    this.createId = function () {
        that.id = guid();
    };

    this.retrieveId = function(){
        that.id = that.properties.uniqueId;
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
            // when success update the node from the graph
            that.properties[propName] = propValue;
            that.setLabel();
            graphsSetMgr.updateGraphItemLabels(that.id,that.label);
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
    /// initialize
}
