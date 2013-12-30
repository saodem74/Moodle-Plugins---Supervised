// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

M.block_supervised = M.block_supervised || {};

// Code for updating the countdown timer that is used on timed quizzes.
M.block_supervised.timer = {
    // YUI object.
    Y: null,

    // Timestamp at which time runs out, according to the student's computer's clock.
    endtime: 0,

    // This records the id of the timeout that updates the clock periodically,
    // so we can cancel.
    timeoutid: null,

    /**
     * @param Y the YUI object
     * @param duration, timer duration, in secondss.
     */
    init: function(Y, duration) {
        M.block_supervised.timer.Y = Y;
        M.block_supervised.timer.endtime = M.pageloadstarttime.getTime() + duration*1000;
        M.block_supervised.timer.update();

    },

    /**
     * Stop the timer, if it is running.
     */
    stop: function(e) {
        if (M.block_supervised.timer.timeoutid) {
            clearTimeout(M.block_supervised.timer.timeoutid);
        }
    },


    // Function to update the clock with the current time left, and submit the quiz if necessary.
    update: function() {
        var Y = M.block_supervised.timer.Y;
        var secondsleft = Math.floor((M.block_supervised.timer.endtime - new Date().getTime())/1000);

        // If time has expired, finish session simulating mouse click by form button.
        if (secondsleft < 0) {
            M.block_supervised.timer.stop(null);
            M.core_formchangechecker.set_form_submitted();
            YUI().use('node-event-simulate', function(Y) {
                Y.one("*[name=supervised_finishbtn]").simulate("click");
            });
            return;
        }

        // Arrange for this method to be called again soon.
        M.block_supervised.timer.timeoutid = setTimeout(M.block_supervised.timer.update, 1000);
    }
};