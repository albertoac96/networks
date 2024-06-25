<template>
    <div ref="p5Container"></div>
  </template>
  
  <script>
  import p5 from 'p5';
  
  export default {
    name: 'NodeNetwork',
    data() {
      return {
        sketch: null,
        nodes: [],
        numNodes: 20,
      };
    },
    mounted() {
      this.sketch = new p5(this.createSketch, this.$refs.p5Container);
    },
    beforeDestroy() {
      this.sketch.remove();
    },
    methods: {
      createSketch(p) {
        p.setup = () => {
          p.createCanvas(p.windowWidth, p.windowHeight);
          for (let i = 0; i < this.numNodes; i++) {
            this.nodes.push(new Node(p, p.random(p.width), p.random(p.height)));
          }
        };
  
        p.draw = () => {
          p.background(0);
          for (let node of this.nodes) {
            node.move();
            node.display();
            for (let other of this.nodes) {
              if (node !== other && node.isClose(other)) {
                p.stroke(255, 150);
                p.line(node.x, node.y, other.x, other.y);
              }
            }
          }
        };
  
        class Node {
          constructor(p, x, y) {
            this.p = p;
            this.x = x;
            this.y = y;
            this.vx = p.random(-2, 2);
            this.vy = p.random(-2, 2);
            this.radius = 5;
          }
  
          move() {
            this.x += this.vx;
            this.y += this.vy;
  
            if (this.x < 0 || this.x > this.p.width) this.vx *= -1;
            if (this.y < 0 || this.y > this.p.height) this.vy *= -1;
          }
  
          display() {
            this.p.noStroke();
            this.p.fill(255);
            this.p.circle(this.x, this.y, this.radius * 2);
          }
  
          isClose(other) {
            let distance = this.p.dist(this.x, this.y, other.x, other.y);
            return distance < 100;
          }
        }
      }
    }
  };
  </script>
  
  <style>
  body {
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
  canvas {
    display: block;
  }
  </style>
  