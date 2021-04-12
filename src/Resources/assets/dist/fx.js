"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.slideToggle = exports.slideDown = exports.slideUp = exports.fadeOut = void 0;

// @see https://gist.github.com/alirezas/c4f9f43e9fe1abba9a4824dd6fc60a55
var fadeOut = function fadeOut(el, cb) {
  el.style.opacity = 1;

  (function fade() {
    if ((el.style.opacity -= .1) < 0) {
      el.style.display = 'none';
      cb();
    } else {
      requestAnimationFrame(fade);
    }
  })();
}; // @see https://gist.github.com/skttl/b8ea597ebf2db66a3e2a06491f7b4029


exports.fadeOut = fadeOut;

var slideUp = function slideUp(target) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.boxSizing = 'border-box';
  target.style.height = target.offsetHeight + 'px';
  target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  window.setTimeout(function () {
    target.style.display = 'none';
    target.style.removeProperty('height');
    target.style.removeProperty('padding-top');
    target.style.removeProperty('padding-bottom');
    target.style.removeProperty('margin-top');
    target.style.removeProperty('margin-bottom');
    target.style.removeProperty('overflow');
    target.style.removeProperty('transition-duration');
    target.style.removeProperty('transition-property');
  }, duration);
};

exports.slideUp = slideUp;

var slideDown = function slideDown(target) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  target.style.removeProperty('display');
  var display = window.getComputedStyle(target).display;

  if (display === 'none') {
    display = 'block';
  }

  target.style.display = display;
  var height = target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = 'border-box';
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.height = height + 'px';
  target.style.removeProperty('padding-top');
  target.style.removeProperty('padding-bottom');
  target.style.removeProperty('margin-top');
  target.style.removeProperty('margin-bottom');
  window.setTimeout(function () {
    target.style.removeProperty('height');
    target.style.removeProperty('overflow');
    target.style.removeProperty('transition-duration');
    target.style.removeProperty('transition-property');
  }, duration);
};

exports.slideDown = slideDown;

var slideToggle = function slideToggle(target) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;

  if ('none' === window.getComputedStyle(target).display) {
    return slideDown(target, duration);
  }

  return slideUp(target, duration);
};

exports.slideToggle = slideToggle;