/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/*
 * NodeCollection Class
 */

function nodeCollection() {

    var that = this;

    this.nodes = {};

    this.edges = {};

    this.nodeIds = [];

    this.edgeIds = [];

    this.singleSelection = false;

    this.addNodeToCollection = function (node, isNew) {
        
        if (isNew) {
            node.createId();
        }
        node.updateTemplateProperties();
        that.nodes[node.id] = node;
        that.nodeIds.push(node.id);
    };

    this.selectNode = function (nodeId) {
        // list propertie
        propertyList.loadProperties(that.nodes[nodeId].properties);
        propertyList.switchPropertyTargetType("Node");
        that.singleSelection = true;
        that.singleSelectionType = "node";
        that.selectedNode = that.nodes[nodeId];
    };

    this.selectEdge = function (edgeId) {
        // list propertie
        propertyList.loadProperties(that.edges[edgeId].properties);
        propertyList.switchPropertyTargetType("Edge");
        that.singleSelection = true;
        that.singleSelectionType = "edge";
        that.selectedEdge = that.edges[edgeId];
    };

    this.unselectNodes = function () {
        propertyList.emptyMode();
        that.singleSelection = false;
        that.selectedNode = null;
    };

    this.createRelationship = function (source, target, type) {
        var newEdge = new Edge(source, target, type, true);
        that.edges[newEdge.id] = newEdge;
        that.edgeIds.push(newEdge.id);
        $.post(ajaxUrl, {
            action: "link_add",
            uniqueIdSource: source,
            uniqueIdTarget: target,
            linkType: type,
            uniqueId: newEdge.id
        }).success(function (data) {
            // when success delete the node from the graph
            console.log(data);
            // add to graph
            var activeGraph = getActiveGraph();
            activeGraph.graphAddEdge(source, target, newEdge.id, type);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.loadNodeTypeCollection = function (nodeType, graph) {
        $.post(ajaxUrl, {
            action: "nodeTypeQuery",
            nodeType: nodeType
        }).success(function (data) {
            // when success delete the node from the graph
            // add to graph
            graph.displayNodeTypeList(data.nodes, data.edges, nodeType);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.loadRelationships = function (graph, direction) {
        var nodeId = that.selectedNode.id;
        $.post(ajaxUrl, {
            action: "getRelationships",
            nodeId: nodeId,
            direction: direction
        }).success(function (data) {
            // when success delete the node from the graph
            // add to graph
            if ((data.nodes.length > 0) || (data.edges.length > 1)) {
                graph.displayNodeTypeList(data.nodes, data.edges);
            } else {
                standardNotification("no relationships found","warning");
            }

        }).fail(function (err) {
            console.log(err);
        });
    };
}