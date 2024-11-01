<?php

/**
 * @package Popup
 */

defined('ABSPATH') || exit;
?>



<!-- html  -->
<div class="_wppool-popup" id="_wppool-popup" style="display: none;" data-plugin="wp_dark_mode" tabindex="1">
    <div class="_wppool-popup-overlay"></div>
    <div class="_wppool-popup-modal">

        <!-- close  -->
        <div class="_wppool-popup-modal-close">
            &times;
        </div>

        <!-- content section  -->
        <div class="_wppool-popup-modal-footer">

            <!-- countdown  -->
            <div class="_wppool-popup-countdown">
                <span class="_wppool-popup-countdown-text"><?php echo __('Deal Ends In', 'wppool'); ?></span>
                <div class="_wppool-popup-countdown-time">
                    <div>
                        <span data-counter="days">00</span>
                        <span><?php echo __('Days', 'wppool'); ?></span>
                    </div>
                    <span>:</span>
                    <div>
                        <span data-counter="hours">00</span>
                        <span><?php echo __('Hours', 'wppool'); ?></span>
                    </div>
                    <span>:</span>
                    <div>
                        <span data-counter="minutes">00</span>
                        <span><?php echo __('Minutes', 'wppool'); ?></span>
                    </div>
                    <span>:</span>
                    <div>
                        <span data-counter="seconds">00</span>
                        <span><?php echo __('Seconds', 'wppool'); ?></span>
                    </div>
                </div>
            </div>

            <!-- button  -->
            <a class="_wppool-popup-button" href=""><?php echo __('Upgrade to Pro', 'wppool'); ?></a>
        </div>

    </div>


</div>

<!-- css  -->

<style>
    :root {
        --wppool-popup-color: #FF631A;
    }

    ._wppool-popup * {
        all: initial;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    ._wppool-popup {
        position: fixed;
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        border: 0;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999999 !important;
    }


    ._wppool-popup-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
    }

    ._wppool-popup-modal {
        width: 600px;
        max-width: 600px !important;
        height: 600px;
        max-height: 600px !important;
        color: white;
        background: #222 url('<?php echo plugin_dir_url(__FILE__); ?>/Image.png');
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        padding: 0;
        margin: 0;
        transform: scale(0.9);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        border-radius: 3px;
        box-shadow: 0 0 10px 0 rgb(0 0 0 / 50%);
    }

    ._wppool-popup-modal-close {
        position: absolute;
        top: 5px;
        right: 10px;
        font-size: 50px;
        cursor: pointer;
        color: var(--wppool-popup-color);
        transition: .3s;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        padding: 0;
        margin: 0;
        opacity: .5;
    }

    ._wppool-popup-modal-close:hover {
        opacity: 1;
    }

    ._wppool-popup-modal-footer {
        width: 100%;
        height: 225px;
        max-height: 225px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 15px 0;
    }

    ._wppool-popup-countdown {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-evenly;
        gap: 10px;
    }

    ._wppool-popup-countdown-text {
        font-size: 14px;
        font-weight: 600;
        color: white;
        position: relative;
        line-height: 1.2;
    }

    ._wppool-popup-countdown-time {
        display: flex;
        align-items: center;
        justify-content: space-evenly;
        gap: 10px;
    }

    ._wppool-popup-countdown-time>div {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-evenly;
        gap: 5px;
    }

    ._wppool-popup-countdown-time>div>span {
        font-size: 20px;
        font-weight: 600;
        color: white;
    }

    ._wppool-popup-countdown-time>div>span:nth-child(1) {
        border: 2px solid rgba(255, 255, 255, 0.6);
        height: 40px;
        width: 45px;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }

    ._wppool-popup-countdown-time>div>span:nth-child(2) {
        font-size: 12px;
        font-weight: 500;
        color: rgb(255, 255, 255 / .8);
    }

    ._wppool-popup-countdown-time>span {
        font-size: 50px;
        color: white;
        margin-top: -25px;
    }

    ._wppool-popup-button {
        height: 50px;
        background: var(--wppool-popup-color);
        color: #222;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 5px;
        cursor: pointer;
        transition: .3s;
        color: white;
        padding: 0 30px;
        margin: 20px 0;
        transition: .2s;
        position: relative;
    }

    ._wppool-popup-button:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: .3s;
    }

    ._wppool-popup-button:hover {
        color: white;
    }

    ._wppool-popup-button:hover:after {
        width: 100%;
    }


    /* responsive  */
    @media (max-width: 576px) {
        ._wppool-popup-countdown {
            transform: scale(.99);
        }
    }
</style>


<!-- js  -->

<script>
    (function($) {

        // return if it's already written 
        if (typeof(WPPOOL) !== 'undefined') return;


        const $container = $("#_wppool-popup");

        // class Popup 
        class Popup {
            /**
             * Plugin slug
             */
            name = "wp_dark_mode"

            /**
             * Events
             */
            events = {}

            /**
             * Constructor
             */
            constructor(name) {
                this.name = name;
            }

            /**
             * Register Event
             * @param {string} event
             * @param {function} callback
             */
            on(event, callback) {
                if (typeof(this.events[event]) === 'undefined') {
                    this.events[event] = [];
                }

                this.events[event].push(callback);
            }

            /**
             * Trigger Event
             * @param {string} event
             * @param {array} args
             */
            trigger(event, args = []) {
                if (typeof(this.events[event]) !== 'undefined') {
                    this.events[event].forEach(callback => {
                        callback.apply(this, args);
                    });
                }
            }

            /**
             * Register events
             */
            registerEvents() {
                // close container on click overlay 
                $(document).on('click', '[data-plugin="' + this.name + '"] ._wppool-popup-overlay', (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    this.trigger("overlayClick", [event, this.data]);
                    this.hide();
                });

                // close container on click close button
                $(document).on('click', '[data-plugin="' + this.name + '"] ._wppool-popup-modal-close', (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    this.trigger("closeClick", [event, this.data]);
                    this.hide();
                });

                // on click on button 
                $(document).on('click', '[data-plugin="' + this.name + '"] ._wppool-popup-button', (event) => {

                    event.preventDefault();
                    event.stopPropagation();

                    // trigger event close
                    this.trigger('buttonClick', [this.data]);

                    // close modal 
                    this.hide();

                    // navigate to data.button_url if button url is not empty 
                    let url = this.data.button_url || null;

                    // check the url is valid 
                    if (url && url.length > 0) {
                        // open url in new tab 
                        window.open(url, '_blank');
                    }

                });

                // on click on modal 
                $(document).on('click', '[data-plugin="' + this.name + '"] ._wppool-popup-modal', (event) => {
                    event.stopPropagation();

                    this.trigger('click', [this.data]);
                });

                // close on esc key 
                // close modal on press esc key 
                $(document).on('keyup', (e) => {
                    if (e.keyCode == 27) {
                        this.hide();
                    }
                });
            }

            /**
             * Destroy events 
             */
            destroyEvents() {
                $(document).off('click', '[data-plugin="' + this.name + '"] ._wppool-popup-overlay');
                $(document).off('click', '[data-plugin="' + this.name + '"] ._wppool-popup-modal-close');
                $(document).off('click', '[data-plugin="' + this.name + '"] ._wppool-popup-button');
                $(document).off('click', '[data-plugin="' + this.name + '"] ._wppool-popup-modal');
                $(document).off('keyup');
            }

            /**
             * To Slug
             */
            toSlug(str) {
                return str.toLowerCase().replace(/ /g, '_').replace(/[^\w-]+/g, '');
            }



            /**
             * Get Plugin by name
             * @param {string} name  
             * @return {object} plugin 
             */
            getPlugin(name) {
                return WPPOOL.plugins.find(plugin => this.toSlug(plugin.name) === name);
            }

            /**
             * Get Plugin Data
             */
            get data() {
                return this.getPlugin(this.name);
            }

            /**
             * Show Popup
             */
            show() {
                if (!this.data) return;

                this.registerEvents();

                this.setPopupData(this.data);

                // const 7 days in seconds = 604800;
                const sevenDays = 604800;
                this.initCounter(this.data.counter_time || sevenDays);

                // $container show with soft bounce 
                $container.fadeIn(100);

                // trigger event 
                this.trigger('show', [this.data]);
            }

            /**
             * Close
             */
            hide() {
                $container.fadeOut(100);
                // trigger event close
                this.trigger('hide', [this.data]);

                this.destroyEvents();
            }


            /**
             * Set Popup Data
             */
            setPopupData(data) {

                const defaultData = this.getPlugin('wp_dark_mode');

                // change background image 

                $container.find("._wppool-popup-modal").css({
                    "background-image": `url(${data.image_url || ''})`
                });



                // check if the image url is valid image url online
                const fallback_image_url = '<?php echo plugin_dir_url(__FILE__); ?>/Image.png';

                if (data.image_url && data.image_url.length > 0) {
                    // check if the image url is valid image url online                     
                    fetch(data.image_url).then(response => {
                        if (!response.ok) {
                            $container.find("._wppool-popup-modal").css({
                                "background-image": `url(${fallback_image_url})`
                            });
                        }
                    }).catch(error => {
                        // set default image 
                        $container.find("._wppool-popup-modal").css({
                            "background-image": `url(${fallback_image_url})`
                        });
                    });
                }

                // set button text 
                $container.find("._wppool-popup-button").text(data.button_text || 'GET NOW');

                // set button link
                $container.find("._wppool-popup-button").attr("href", data.button_link || '');

                // set popup color
                $container.find("._wppool-popup-modal").css({
                    "--wppool-popup-color": data.color || "#FF631A"
                });

                // set data plugin 

                $container.attr("data-plugin", this.name);


                // focus data-plugin 
                $container.find("[data-plugin]").focus();

            }


            /**
             * Update Counter 
             * @param {string} time  
             */

            updateCounter(seconds) {
                const $counter = $container.find("._wppool-popup-countdown-time");
                const $days = $counter.find("[data-counter='days']");
                const $hours = $counter.find("[data-counter='hours']");
                const $minutes = $counter.find("[data-counter='minutes']");
                const $seconds = $counter.find("[data-counter='seconds']");

                const days = Math.floor(seconds / (3600 * 24));
                seconds -= days * 3600 * 24;
                const hrs = Math.floor(seconds / 3600);
                seconds -= hrs * 3600;
                const mnts = Math.floor(seconds / 60);
                seconds -= mnts * 60;

                $days.text(days);
                $hours.text(hrs);
                $minutes.text(mnts);
                $seconds.text(seconds);
            }


            /**
             * initCounter
             */
            initCounter(last_date) {

                const countdown = () => {

                    // system time 
                    const now = new Date().getTime();

                    // set end time to 11:59:59 PM 
                    const endDate = new Date(last_date);
                    endDate.setHours(23);
                    endDate.setMinutes(59);
                    endDate.setSeconds(59);

                    const seconds = Math.floor((endDate.getTime() - now) / 1000);

                    if (seconds < 0) {
                        return false;
                    }

                    this.updateCounter(seconds);

                    return true;
                }

                let result = countdown();

                if (result) {
                    this.trigger("countdownStart", [this.data]);
                } else {
                    this.trigger("countdownFinish", [this.data]);
                    $container.find("._wppool-popup-countdown").hide(0);
                }

                // update counter every 1 second 
                const counter = setInterval(() => {

                    const result = countdown();

                    if (!result) {
                        clearInterval(counter);
                        this.trigger("counter_end", [this.data]);
                        $container.find("._wppool-popup-countdown").hide(0);
                    }

                }, 1000);


            }

            /**
             * Update counter
             * @param {int} days
             * @param {int} hours
             * @param {int} minutes
             * @param {int} seconds
             */
        }



        // Big Object Theory for WPPOOL
        var WPPOOL = {


            /**
             * Get Plugins
             */
            get plugins() {
                return typeof(WPPOOL_Plugins) !== 'undefined' ? WPPOOL_Plugins.plugins : [];
            },

            /**
             * Plugin 
             * @param String plugin 
             */
            Popup: function(name = '') {
                if (name) {
                    return new Popup(name);
                }

                return false;
            },

            /**
             * Depceated 
             */

            /**
             * Plugin 
             * @param String plugin 
             */
            Plugin: function(name = '') {
                return this.Popup(name);
            },

            /**
             * Debug log
             */
            Log : function () {
                if (typeof(WPPOOL_Plugins) === 'undefined' || WPPOOL_Plugins.debug != 1) return;

                let args = Array.from(arguments);
                console.log(
                    "%cwppool",
                    "background: #0080ca; color: white; font-size: 9px; padding: 2px 4px; border-radius: 2px;",
                    ...args
                );
            }
        }

        // make WPPOOL global 

        window.WPPOOL = WPPOOL;

        /**
         * Init
         */

        WPPOOL.Log({
            "Plugins": WPPOOL.plugins,
            "Debug": WPPOOL_Plugins.debug
        });

    })(jQuery)
</script>