/*!
* SpamFormProtection 1.0.0
* 
* @author: DoMedia
* @description: A lightweight, vanilla JavaScript library for detecting bot-like form interactions
* @license: MIT
* 
* Key Features:
* - Human/bot behavior tracking
* - Customizable threshold time (Minimum time required for a human to fill the form) 
* - Compatible with AMD, CommonJS, and global browser environments
* - Monitoring individual textareas to detect unusually fast typing behavior
* - Dynamic honeypot addition
* - Dynamic POST method addition
* - Brute force spam prevention
* - Suspicious user enviornment detection
* - Weighted score system for bot detection
* - User verification popup as a failsafe
* 
* - Usage Example:
*       initSpamProtection(5); 
*       // Initializes spam protection with a 5-second threshold. 
*       // To update the threshold, call initSpamProtection(newThreshold) with the desired value (e.g., 5 seconds).
*       
*       const isHumanScore = verifyHumanity(); 
*       // Returns a boolean indicating user behavior:
*       // true - Likely human, false - Likely bot.
*       // Use this to verify user behavior before processing form submissions.
* 
*   Note: If your form submit button does not have a `type="submit"` attribute, 
*   add the `data-sfp` data attribute to ensure compatibility.
*
* Dependencies: None
* 
* Copyright (c) 2024 DoMedia
* Released under the MIT License
*/

(function (global, factory) {
    "use strict";

    if (typeof module === "object" && typeof module.exports === "object") {
        // CommonJS/Node.js-like environment
        module.exports = global.document
            ? factory(global, true)
            : function (w) {
                if (!w.document) {
                    throw new Error("Library requires a window with a document");
                }
                return factory(w);
            };
    } else {
        // Browser global environment
        factory(global);
    }
})(typeof window !== "undefined" ? window : this, function (window, noGlobal) {

    document.addEventListener('DOMContentLoaded', function () {

        let movementDetectorScore = 0;

        class DeviceInteractionTracker {
            constructor(options = {}) {
                this.config = {
                    mouseMoveThreshold: 50,
                    timeThreshold: 100,
                    accelerationThreshold: 0.5,
                    ...options
                };

                this.movementData = {
                    mouse: { positions: [], timestamps: [], speeds: [], accelerations: [] }
                };

                this.trackMouseMovement = this.trackMouseMovement.bind(this);

                this.initializeTracking();
            }

            initializeTracking() {

                document.addEventListener('mousemove', this.trackMouseMovement);

            }

            trackMouseMovement(event) {
                const now = Date.now();
                const position = { x: event.clientX, y: event.clientY };
                const mouseData = this.movementData.mouse;

                mouseData.positions.push(position);
                mouseData.timestamps.push(now);

                // Ensure we have at least two points to calculate
                if (mouseData.positions.length > 1) {
                    const prevPosition = mouseData.positions[mouseData.positions.length - 2];
                    const prevTimestamp = mouseData.timestamps[mouseData.timestamps.length - 2];
                    const timeDiff = now - prevTimestamp;

                    // Avoid division by zero or small time differences
                    if (timeDiff > 0) {
                        const distance = this.calculateDistance(prevPosition, position);

                        // Ensure speed isn't excessively high
                        const speed = distance / (timeDiff / 1000); // pixels per second

                        // Optional: Apply a filter to remove unreasonable speeds (e.g., 3000px/sec as a threshold)
                        const maxSpeedThreshold = 3000; // Customize as needed
                        if (speed < maxSpeedThreshold) {
                            mouseData.speeds.push(speed);
                        } else {
                            // If speed exceeds threshold, discard it (or apply smoothing logic)
                            mouseData.speeds.push(0);
                        }

                        // Acceleration calculation
                        if (mouseData.speeds.length > 1) {
                            const prevSpeed = mouseData.speeds[mouseData.speeds.length - 2];
                            const acceleration = Math.abs(speed - prevSpeed) / (timeDiff / 1000); // change in speed over time
                            mouseData.accelerations.push(acceleration);
                        }
                    }
                }
                this.analyzeCursorMovement();
            }

            calculateDistance(point1, point2) {
                return Math.sqrt(Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2));
            }

            analyzeCursorMovement() {
                const mouseData = this.movementData.mouse;
                if (mouseData.positions.length < 2) return null;

                const analysis = {
                    //totalDistance: 0,
                    //averageSpeed: 0,
                    //maxSpeed: 0,
                    averageAcceleration: 0,
                    movementPattern: 'unknown'
                };

                // // Calculate total distance
                // for (let i = 1; i < mouseData.positions.length; i++) {
                //     analysis.totalDistance += this.calculateDistance(mouseData.positions[i - 1], mouseData.positions[i]);
                // }

                // // Calculate average speed and max speed
                // if (mouseData.speeds.length > 0) {
                //     analysis.averageSpeed = mouseData.speeds.reduce((a, b) => a + b, 0) / mouseData.speeds.length;
                //     analysis.maxSpeed = Math.max(...mouseData.speeds);
                // }

                // Calculate average acceleration
                if (mouseData.accelerations.length > 0) {
                    analysis.averageAcceleration = mouseData.accelerations.reduce((a, b) => a + b, 0) / mouseData.accelerations.length;
                }

                analysis.movementPattern = this.classifyMovementPattern(mouseData.positions);
                this.updateBotScore(analysis);
                return analysis;
            }

            // Helper function to calculate variance of angle differences
            calculateVariance(numbers) {
                if (numbers.length === 0) return 0;

                const mean = numbers.reduce((a, b) => a + b, 0) / numbers.length;
                const variance = numbers.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / numbers.length;

                return Math.sqrt(variance); // Return standard deviation
            }

            classifyMovementPattern(positions) {
                // Ignore first 50 mouse movements and require minimum data points
                const IGNORE_INITIAL_MOVEMENTS = 50;
                const MINIMUM_VALID_MOVEMENTS = 60;

                if (positions.length < MINIMUM_VALID_MOVEMENTS) return 'insufficient_data';

                // Slice the array to remove initial movements
                const filteredPositions = positions.slice(IGNORE_INITIAL_MOVEMENTS);

                let totalAngleDeviation = 0;
                const angleDifferences = [];

                for (let i = 2; i < filteredPositions.length; i++) {
                    const prevSegment = {
                        dx: filteredPositions[i - 1].x - filteredPositions[i - 2].x,
                        dy: filteredPositions[i - 1].y - filteredPositions[i - 2].y
                    };

                    const currentSegment = {
                        dx: filteredPositions[i].x - filteredPositions[i - 1].x,
                        dy: filteredPositions[i].y - filteredPositions[i - 1].y
                    };

                    // Calculate angle between segments
                    const angle1 = Math.atan2(prevSegment.dy, prevSegment.dx);
                    const angle2 = Math.atan2(currentSegment.dy, currentSegment.dx);

                    const angleDiff = Math.abs(angle1 - angle2);
                    angleDifferences.push(angleDiff);
                    totalAngleDeviation += angleDiff;
                }

                // Adjust calculations based on filtered positions
                const averageAngleDeviation = totalAngleDeviation / (filteredPositions.length - 2);
                const maxAngleDiff = Math.max(...angleDifferences);

                // Enhanced analysis to handle initial movement noise
                const varianceThreshold = this.calculateVariance(angleDifferences);

                // Tight thresholds to identify bot-like linear movement
                if (maxAngleDiff < Math.PI / 8 &&
                    averageAngleDeviation < Math.PI / 16 &&
                    varianceThreshold < 0.1) {
                    return 'highly_linear'; // Strong indicator of potential bot behavior
                }

                if (maxAngleDiff < Math.PI / 4 &&
                    averageAngleDeviation < Math.PI / 8) {
                    return 'linear'; // More relaxed linear movement
                }

                return 'non_linear';
            }



            updateBotScore(analysis) {

                // Adjust movementScore based on movement patterns and metrics
                if (analysis.movementPattern === 'highly_linear') {
                    // Highly linear movements are most likely bot-like and impossible for a human to replicate, hence larger panelty.
                    movementDetectorScore += 2;
                } else if (analysis.movementPattern === 'linear') {
                    // Relaxed linear movements are more likely bot-like or possibly extreamly precise human mouse movement, hence less panelty
                    movementDetectorScore += 1;
                }

                if (analysis.averageAcceleration < 0.2) {
                    // Extremely smooth acceleration may indicate a bot
                    movementDetectorScore += 1;
                }

               

            }
        }

        //Mouse movement tracking instance
        new DeviceInteractionTracker();

        //SpamProtection core functionality
        (function () {

            let isHumanBehaviour = false;
            let botScore = 0;
            let overrideBehaviour = false;

            /**
             * Spam protection initiating function. 
             * @param {number} thresholdTime - Minimum time taken for a human to fill the form
             */
            function initSpamProtection(thresholdTime = 5) {

                const forms = document.querySelectorAll('form');

                if (forms.length === 0) {
                    //Abort initiating any further
                    return
                }

                //Detecting suspicious enviornment - Adds 0.5 score if detected
                if (navigator.webdriver || (navigator.userAgentData && navigator.userAgentData.brands.length === 0)) {
                    botScore += 0.5;
                }

                forms.forEach(form => {
                    // Local state variables for each form
                    let isHumanScore = 0;
                    let formAttrSet = false;
                    let filledImmediatelyScore = 0;
                    let startTime = null;
                    const textareasData = {};
                    const subBtn = form.querySelector('[type="submit"]') || form.querySelector('button[data-sfp]');

                    //Validating the presence of submit button
                    if (!subBtn) {
                        const formName = form.getAttribute('name');

                        if (!formName) {
                            console.error(`SFP Error: No submit button with the 'data-sfp' attribute was found. Ensure the form's submit button includes the 'data-sfp' attribute.`)
                        } else {
                            console.error(`SFP Error: No submit button with the 'data-sfp' attribute was found in ${formName} form. Ensure the form's submit button includes the 'data-sfp' attribute.`)
                        }
                        return
                    }

                    //Dynamic honeypot addition
                    const honeypotField = document.createElement('input');
                    honeypotField.type = 'text';
                    honeypotField.name = 'SFPHP_' + Math.random().toString(36).substring(7);  // Random name to avoid detection
                    honeypotField.style.cssText = 'position:absolute;  opacity:0; width:1px; height:1px; pointer-events:none;';
                    honeypotField.setAttribute('autocomplete', 'off');
                    form.prepend(honeypotField);

                    // Function to handle input event on textarea fields
                    const handleTextareaInputEvent = (e) => {
                        const textarea = e.target;

                        // Check if the input event is from a textarea
                        if (textarea.tagName.toLowerCase() === "textarea") {
                            const id = textarea.getAttribute("id") || textarea.name || `textarea-${Date.now()}`;

                            // Initialize tracking data for this textarea if not already initialized
                            if (!textareasData[id]) {
                                textareasData[id] = {
                                    startTime: Date.now(), // Timestamp when the user started typing
                                    filledImmediately: 0, // Default flag
                                };
                            }
                        }
                    };

                    // Function to handle focusout (blur) event to calculate time taken to fill the textarea fields
                    const handleTextareaFocusOutEvent = (e) => {
                        const textarea = e.target;

                        // Check if the focusout event is from a textarea
                        if (textarea.tagName.toLowerCase() === "textarea") {
                            const id = textarea.getAttribute("id") || textarea.name || `textarea-${Date.now()}`;

                            if (textareasData[id]) {
                                // Calculate time taken in milliseconds
                                const timeTaken = Date.now() - textareasData[id].startTime;

                                // Set filledImmediately flag to 1 if the user took less than 1.3 seconds
                                textareasData[id].filledImmediately = timeTaken < 1300 ? 1 : 0;


                            }
                        }
                    };

                    form.addEventListener("input", handleTextareaInputEvent);
                    form.addEventListener("focusout", handleTextareaFocusOutEvent);

                    // Click event listener - Dynamically adds POST method
                    form.addEventListener('click', (e) => {
                        try {
                            isHumanScore += 0.01;

                            if (!formAttrSet) {
                                form.setAttribute('method', 'POST');
                                formAttrSet = true;
                            }
                        } catch (error) {
                            console.error('SFP Error: An issue occurred during the form click event:', error);
                        }
                    });

                    // Focus event listener for all form elements
                    form.addEventListener('focusin', (e) => {
                        try {

                            isHumanScore += 0.01; //Small score for interacting with elements

                            if (!formAttrSet) {
                                form.setAttribute('method', 'POST');
                                formAttrSet = true;
                            }
                        } catch (error) {
                            console.error('SFP Error: An issue occurred during the form click event:', error);
                        }
                    });

                    // Input event listener - Records the timestamp when the user begins typing in any form input for the first time.
                    form.addEventListener('input', (e) => {

                        try {

                            if (!startTime) {
                                startTime = Date.now();
                            }
                        } catch (error) {
                            console.error('SFP Error: An issue occurred during the form input event:', error);
                        }
                    });

                    form.addEventListener('submit', (e) => {

                        try {

                            isHumanBehaviour = false;

                            //Time based protection - Calculates the time taken to fill the form
                            if (startTime) {
                                const timeTaken = (Date.now() - startTime) / 1000;

                                //Checks whether user has filled all the required inputs before setting the filledImmediatelyScore flag to 1 - Prevents false positives.
                                const requiredFields = Array.from(form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]'));
                                const allFieldsFilled = requiredFields.every(field => {
                                    // Handle different input types
                                    if (field.type === 'checkbox' || field.type === 'radio') {
                                        return field.checked;
                                    }
                                    return field.value.trim() !== "";
                                });

                                if (allFieldsFilled && timeTaken < thresholdTime) {
                                    filledImmediatelyScore = 1
                                } else {
                                    filledImmediatelyScore = 0;
                                }

                            } else {
                                console.warn('SFP Warning: No input detected before submission.');
                                filledImmediatelyScore = 1;
                            }

                            // Check if honeypot field has been filled
                            if (honeypotField.value.trim() !== '') {
                                botScore += 100; // More weight since only a bot can fill the honeypot
                            }

                            //Check for suspicious movements
                            if (movementDetectorScore > 200) {
                                botScore += 0.95;
                            } else if (movementDetectorScore > 150) {
                                botScore += 0.9;

                            } else if (movementDetectorScore > 100) {
                                botScore += 0.8;
                            }

                            //Calculating final botScore
                            botScore += (filledImmediatelyScore + isHumanScore)


                            //Check whether botScore is within the acceptable value range. Human users typically receive a bot score between 0 and 1 (exclusive). A score of 0 or ≥ 1 indicates bot-like behavior.
                            if (botScore > 0 && botScore < 1) {
                                isHumanBehaviour = true;
                                overrideBehaviour = true;
                                subBtn.click();

                            } else {
                                if (botScore >= 1 && botScore < 100) {
                                    showVerificationPopup(() => {
                                        // This callback is called after successful verification
                                        subBtn.click();

                                    });
                                }

                            }

                            // Prevent form submission if not human behavior
                            if (!isHumanBehaviour && !overrideBehaviour) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                console.warn('Form submission blocked: Bot-like behavior detected');
                                return false;
                            }

                        } catch (error) {
                            console.error('SFP Error: An issue occurred during the form submit button click event:', error);
                            // Prevent form submission on error
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    }, true); //Using event capture true to make sure verification runs first

                });
            }

            function showVerificationPopup(onVerificationSuccess) {
                const humanVerificationBackground = document.createElement('div');
                humanVerificationBackground.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                `;

                const humanVerificationContainer = document.createElement('div');
                humanVerificationContainer.style.cssText = `
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: #fff;
                    border-radius: 10px;
                    padding: 20px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    z-index: 1000;
                    text-align: center;
                `;

                const switchLabel = document.createElement('label');
                switchLabel.style.cssText = `
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 10px;
                    font-weight: bold;
                `;

                const switchTextHeader = document.createElement('span');
                switchTextHeader.textContent = 'Are you a human?';
                switchTextHeader.style.marginBottom = '10px';

                const humanVerificationSwitch = document.createElement('div');
                humanVerificationSwitch.style.cssText = `
                    width: 200px;
                    height: 50px;
                    background-color: #ddd;
                    border-radius: 25px;
                    position: relative;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    padding: 0 10px;
                    transition: background-color 0.3s;
                    font-weight: bold;
                    color: #333;
                `;

                const switchSlider = document.createElement('div');
                switchSlider.style.cssText = `
                    width: 40px;
                    height: 40px;
                    background-color: white;
                    border-radius: 50%;
                    position: absolute;
                    top: 5px;
                    left: 5px;
                    transition: transform 0.3s;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                `;

                const switchText = document.createElement('span');
                switchText.textContent = 'Click me';
                switchText.style.cssText = `
                    margin-left: 10px;
                    flex-grow: 1;
                `;

                humanVerificationSwitch.appendChild(switchText);
                humanVerificationSwitch.appendChild(switchSlider);

                let isVerified = false;

                humanVerificationSwitch.addEventListener('click', () => {
                    if (!isVerified) {
                        botScore = 0.1;
                        overrideBehaviour = true;

                        switchSlider.style.transform = 'translateX(150px)';
                        humanVerificationSwitch.style.backgroundColor = '#4CAF50';
                        switchText.textContent = 'Verified';
                        isVerified = true;

                        // Optional: Close popup after verification
                        setTimeout(() => {
                            document.body.removeChild(humanVerificationBackground);
                            onVerificationSuccess();
                        }, 1000);
                    }
                });

                switchLabel.appendChild(switchTextHeader);
                switchLabel.appendChild(humanVerificationSwitch);
                humanVerificationContainer.appendChild(switchLabel);
                humanVerificationBackground.appendChild(humanVerificationContainer);
                document.body.appendChild(humanVerificationBackground);
            }


            // Initialize spam protection automatically
            initSpamProtection(5);

            // Expose library methods for different module systems
            if (typeof define === "function" && define.amd) {
                // For AMD (RequireJS)
                define(function () {
                    return {
                        initSpamProtection
                       
                    };
                });
            } else if (typeof module === "object" && module.exports) {
                // For CommonJS/Node.js
                module.exports = {
                    initSpamProtection
                   
                };
            } else if (typeof window !== 'undefined') {
                // For Global in browser
                window.initSpamProtection = initSpamProtection;
            }
        })();
    })


});
