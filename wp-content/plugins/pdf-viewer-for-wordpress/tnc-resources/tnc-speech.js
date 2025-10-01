document.addEventListener('DOMContentLoaded', function () {
    let isSpeaking = false;
    let isPaused = false;
    let isStop = false;
    let currentPageLines = []; // Store lines of text for the current page
    let selectedVoice = null;
    let speechRate = 1; // Default rate
    let speechPitch = 1; // Default pitch

    const voiceSelect = document.getElementById('voiceSelect');
    const defaultVoiceNameStartsWith = tnc_pdf_voice_model; // Specify your default voice name here

    // Function to refine the text (example logic for refinement)
    function refineText(text) {
        return text.replace(/\x00/g, "").replace(/\s+/g, " ").trim();
    }

    // Populate voice selection dropdown
    function populateVoiceList() {
        const voices = speechSynthesis.getVoices();
        voiceSelect.innerHTML = ''; // Clear previous options
        let foundDefaultVoice = false;

        voices.forEach((voice, index) => {
            if (voice.lang.startsWith(tnc_pdf_language_code)) { // Filter voices by language
                const option = document.createElement('option');
                option.textContent = `${voice.name} (${voice.lang})`;
                option.value = index; // Use index to reference the voice

                // Check for the default voice
                if (!foundDefaultVoice && voice.name.startsWith(defaultVoiceNameStartsWith)) {
                    option.textContent += ' [Default]';
                    option.selected = true; // Mark as selected
                    selectedVoice = voice; // Set the default voice
                    foundDefaultVoice = true;
                }

                voiceSelect.appendChild(option);
            }
        });

        // Fallback: Select the first voice if no default is explicitly marked
        if (!foundDefaultVoice && voiceSelect.options.length > 0) {
            voiceSelect.options[0].selected = true;
            selectedVoice = voices[voiceSelect.options[0].value];
        }
    }

    // Update the selected voice when the dropdown changes
    voiceSelect.addEventListener('change', function () {
        const voices = speechSynthesis.getVoices();
        selectedVoice = voices[this.value];
    });

    // Function to speak the entire page text
    function speakFullPageText(textContent) {
        const refinedText = refineText(textContent);
        const utterance = new SpeechSynthesisUtterance(refinedText);
        const pageNumber = PDFViewerApplication.pdfViewer.currentPageNumber;

        utterance.rate = speechRate;
        utterance.pitch = speechPitch;

        if (selectedVoice) {
            utterance.voice = selectedVoice;
        }

        utterance.onstart = () => {
            isSpeaking = true;
            updateButtonStates();
        };

        utterance.onend = () => {
            const pdfViewer = PDFViewerApplication.pdfViewer;
            if (pdfViewer.currentPageNumber < pdfViewer.pagesCount) {
                moveToNextPage();
            } else {
                isSpeaking = false;
                updateButtonStates();
            }
        };

        utterance.onboundary = (event) => {
            highlightSpokenText(event.charIndex, pageNumber);
        };

        speechSynthesis.speak(utterance);
    }

    function getTextLayerDataByPage(pageNumber) {
        const page = document.querySelector(`div.page[data-page-number="${pageNumber}"]`);
        if (!page) return;
        return page.querySelectorAll('div.textLayer span');
    }

    function highlightSpokenText(charIndex, pageNumber) {
        document.querySelectorAll('.highlight').forEach(element => {
            element.classList.remove('highlight');
        });

        let charCounter = 0;
        const textElements = getTextLayerDataByPage(pageNumber);
        for (let span of textElements) {
            const spanText = span.innerText;
            charCounter += spanText.length;
            if (charCounter >= charIndex) {
                span.classList.add('highlight');
                break;
            }
        }
    }

    function updateButtonStates() {
        document.getElementById('pauseButton').disabled = !isSpeaking || isPaused;
        document.getElementById('resumeButton').disabled = !isSpeaking || !isPaused;
        document.getElementById('stopButton').disabled = !isSpeaking;
    }

    async function extractTextFromCurrentPage() {
        if (!PDFViewerApplication.pdfDocument) {
            alert('No PDF loaded.');
            return '';
        }

        const page = await PDFViewerApplication.pdfDocument.getPage(PDFViewerApplication.pdfViewer.currentPageNumber);
        const textContent = await page.getTextContent();

        currentPageLines = textContent.items
            .filter(item => item.str.trim() !== "")
            .map(item => item.str);

        return currentPageLines.join(' ');
    }

    async function readCurrentPage() {
        const pageText = await extractTextFromCurrentPage();

        if (pageText.trim() === '') {
            alert('No text found on the current page.');
        } else {
            speakFullPageText(pageText);
        }
    }

    function moveToNextPage() {
        const pdfViewer = PDFViewerApplication.pdfViewer;
        if (pdfViewer.currentPageNumber < pdfViewer.pagesCount) {
            pdfViewer.currentPageNumber++;
            readCurrentPage();
        }
    }

    // Stop button functionality
    document.getElementById('stopButton').addEventListener('click', () => {
        // Cancel ongoing speech synthesis
        if (speechSynthesis.speaking || speechSynthesis.paused) {
            speechSynthesis.cancel();
        }

        // Reset states
        isSpeaking = false;
        isPaused = false;
        isStop = true;

        // Reset button states
        updateButtonStates();

        // Optionally reset any highlights
        document.querySelectorAll('.highlight').forEach(element => {
            element.classList.remove('highlight');
        });
    });

    // Update speech rate and pitch dynamically
    document.getElementById('rate').addEventListener('input', function () {
        speechRate = parseFloat(this.value);
        document.getElementById('rateValue').textContent = speechRate.toFixed(1);
    });

    document.getElementById('pitch').addEventListener('input', function () {
        speechPitch = parseFloat(this.value);
        document.getElementById('pitchValue').textContent = speechPitch.toFixed(1);
    });

    // Event listeners
    document.getElementById('speakButton').addEventListener('click', async () => {
        if (isSpeaking) {
            alert('Already reading. Please pause or stop first.');
            return;
        }
        await readCurrentPage();
    });

    document.getElementById('pauseButton').addEventListener('click', () => {
        if (speechSynthesis.speaking && !isPaused) {
            speechSynthesis.pause();
            isPaused = true;
            updateButtonStates();
        }
    });

    document.getElementById('resumeButton').addEventListener('click', () => {
        if (speechSynthesis.speaking && isPaused) {
            speechSynthesis.resume();
            isPaused = false;
            updateButtonStates();
        }
    });

    window.addEventListener('beforeunload', () => {
        if (isSpeaking) {
            speechSynthesis.cancel();
        }
    });

    speechSynthesis.onvoiceschanged = populateVoiceList;
    populateVoiceList();

    PDFViewerApplication.eventBus.on('pagechanging', async () => {
        if (isSpeaking) {
            speechSynthesis.cancel();
        }
        await readCurrentPage();
    });
});

jQuery(document).ready(function ($) {
    // Toggle visibility when #tncHeadphone is clicked
    $('#tncHeadphone').on('click', function () {
        const $wrapper = $('.tnc-synthesis-wrapper');
        $wrapper.toggleClass('show');
    });

    // Hide when .tnc-synthesis-close-icon is clicked
    $('.tnc-synthesis-close-icon').on('click', function () {
        $('.tnc-synthesis-wrapper').removeClass('show');
    });
});

// Responsive hide/show transcribe fields
$(document).ready(function () {
    if (window.innerWidth <= 768) {
        if (tnc_pdf_speed_responsive == '0') {
            $('.tnc_pdf_rate').css('display', 'none');
        }

        if (tnc_pdf_pitch_responsive == '0') {
            $('.tnc_pdf_pitch').css('display', 'none');
        }

        if (tnc_pdf_voice_responsive == '0') {
            $('.tnc_pdf_voice').css('display', 'none');
        }
    }
});
