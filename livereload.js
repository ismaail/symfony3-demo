/*jshint strict:true, browser:true, jquery:true, devel:true, curly:true, eqeqeq:true, immed:true, latedef:true, plusplus:true, undef:true, unused:true, laxbreak:true, nonew:false */
/* global require, __dirname */
(function() {
	"use strict";

	var livereload = require('livereload');
	var server = livereload.createServer({
		exts: ["twig", "css", "js", "jpg", "jpeg", "png", "gif", "ico"],
		port: 35729
	});

	server.watch([
		__dirname + "/app/Resources/views",
		__dirname + "/web"
	]);
}());
