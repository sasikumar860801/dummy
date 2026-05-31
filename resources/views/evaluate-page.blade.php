@extends('layouts.app')

@section('title', $seo['title'])

@section('meta_description', $seo['meta_description'])

@section('meta_keywords', $seo['meta_keywords'])

@section('canonical_url', $seo['canonical_url'])

@section('content')
<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">

    <!-- Breadcrumb -->
    <div style="padding: 20px 0; font-size: 14px; overflow-x: auto;">
        <nav aria-label="breadcrumb">
            <ol style="display: flex; gap: 8px; list-style: none; flex-wrap: wrap;">
                <li><a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">Home</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li><a href="{{ url('/sell-old-phone') }}" style="color: #3b82f6; text-decoration: none;">Sell Mobile</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li><a href="{{ url('/sell-old-phone/sell-' . ($brand->sef_url ?? '')) }}" style="color: #3b82f6; text-decoration: none;">{{ $brand->title ?? 'Brand' }}</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li><a href="{{ url('/sell-old-mobile-phone/used-' . $model->sef_url) }}" style="color: #3b82f6; text-decoration: none;">{{ $model->title }}</a></li>
                <li><i class="fas fa-chevron-right" style="font-size: 10px; color: #64748b;"></i></li>
                <li style="color: #64748b;">Evaluate</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; color: white; margin-bottom: 10px;">Let's Evaluate your device</h1>
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($model->model_img && file_exists(public_path('media/images/model/' . $model->model_img)))
                            <img src="{{ $model->model_img_url }}" alt="{{ $model->title }}" style="width: 35px; height: 35px; object-fit: contain;">
                        @else
                            <i class="fas fa-mobile-alt" style="font-size: 25px; color: #3b82f6;"></i>
                        @endif
                        <span style="color: #cbd5e1; font-size: 16px; font-weight: 600;">{{ $brand->title ?? '' }} {{ $model->title }} ({{ $capacity }})</span>
                    </div>
                </div>
            </div>
            <button id="viewSummaryBtn" style="background: transparent; border: 1px solid #3b82f6; color: #3b82f6; padding: 10px 20px; border-radius: 40px; cursor: pointer; font-size: 14px;">
                View Evaluation Summary <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            </button>
        </div>
    </div>

    <!-- Progress Steps Indicator -->
    <div id="progressSteps" style="margin-bottom: 30px; display: none;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <div style="background: #1e293b; padding: 12px 24px; border-radius: 40px;">
                <span style="color: #94a3b8;">Step <span id="currentStepNum">1</span> of <span id="totalStepsNum">0</span></span>
                <span style="color: #3b82f6; margin-left: 15px;" id="stepName"></span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="evaluate-grid">
        
        <!-- Left Column - Questions & Attributes -->
        <div class="questions-column">
            <!-- Screening Questions Container (Page 1) -->
            <div id="questionsContainer">
                @foreach($qaQuestions as $index => $question)
                <div class="question-card" data-id="{{ $question->id }}" data-index="{{ $index }}" style="margin-bottom: 25px; background: #111118; border: 1px solid #2a2a3a; border-radius: 20px; padding: 25px; display: block;">
                    <div class="question-header">
                        <div class="question-number">
                            <span class="number-badge">{{ $index + 1 }}</span>
                            <h3 class="question-title">{{ $question->name }}</h3>
                        </div>
                    </div>
                    <p class="question-desc">{{ $question->description }}</p>
                    
                    <div class="answer-buttons">
                        <button type="button" class="answer-btn yes-btn" data-id="{{ $question->id }}" data-name="{{ $question->name }}" data-value="yes">
                            <i class="fas fa-check-circle"></i> Yes
                        </button>
                        <button type="button" class="answer-btn no-btn" data-id="{{ $question->id }}" data-name="{{ $question->name }}" data-value="no">
                            <i class="fas fa-times-circle"></i> No
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Multi-Step Attributes Section (shown after all Yes answers) -->
            <div id="attributesSection" style="display: none;">
                <!-- Current Step Group Title -->
                <div id="currentGroupTitle" style="margin-bottom: 25px;">
                    <h2 style="color: white; font-size: 24px; margin-bottom: 10px;" id="groupHeading"></h2>
                    <p style="color: #94a3b8;" id="groupDescription"></p>
                </div>
                
                <!-- Attributes Container for current group -->
                <div id="attributesContainer"></div>
            </div>

            <!-- Navigation Buttons -->
            <div class="action-buttons">
                <button id="prevBtn" class="prev-btn" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button id="nextGroupBtn" class="next-btn" style="display: none;">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button id="finalBtn" class="final-btn" style="display: none;">
                    Finish Evaluation <i class="fas fa-check-circle"></i>
                </button>
            </div>
        </div>

        <!-- Right Column - Summary Sidebar -->
        <div class="summary-sidebar" id="summarySidebar">
            <div class="summary-header">
                <h3>Device Evaluation</h3>
                <button class="close-summary" id="closeSummaryBtn" style="display: none;">&times;</button>
            </div>
            
            <div class="summary-content">
                <h4>Summary</h4>
                <div id="screeningQuestions">
                    <h5>Screening Question:</h5>
                    <div id="summaryList" class="summary-list">
                        <p class="empty-message">No questions answered yet</p>
                    </div>
                </div>
                <div id="attributesSummary" style="display: none;">
                    <h5>Device Condition:</h5>
                    <div id="attributesList" class="attributes-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Summary Modal -->
<div id="mobileSummaryModal" class="mobile-modal" style="display: none;">
    <div class="mobile-modal-content">
        <div class="mobile-modal-header">
            <h3>Device Evaluation Summary</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="mobile-modal-body">
            <div id="mobileSummaryContent"></div>
        </div>
    </div>
</div>

<style>
    .evaluate-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
    }
    
    .question-card {
        transition: all 0.3s ease;
    }
    
    .question-number {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    
    .number-badge {
        background: #3b82f6;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        color: white;
        flex-shrink: 0;
    }
    
    .question-title {
        color: white;
        font-size: 18px;
        margin: 0;
    }
    
    .question-desc {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 20px;
        margin-left: 44px;
        line-height: 1.5;
    }
    
    .answer-buttons {
        display: flex;
        gap: 15px;
        margin-left: 44px;
    }
    
    .answer-btn {
        padding: 10px 25px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .yes-btn {
        background: transparent;
        border: 2px solid #10b981;
        color: #10b981;
    }
    
    .yes-btn:hover, .yes-btn.active {
        background: #10b981;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16,185,129,0.3);
    }
    
    .no-btn {
        background: transparent;
        border: 2px solid #ef4444;
        color: #ef4444;
    }
    
    .no-btn:hover, .no-btn.active {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(239,68,68,0.3);
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        margin-bottom: 40px;
        gap: 15px;
    }
    
    .prev-btn, .next-btn, .final-btn {
        padding: 14px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }
    
    .prev-btn {
        background: transparent;
        border: 1px solid #64748b;
        color: #64748b;
    }
    
    .prev-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateX(-2px);
    }
    
    .next-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
    }
    
    .next-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59,130,246,0.4);
    }
    
    .final-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .final-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(16,185,129,0.4);
    }
    
    /* Attribute Card Styles - Grid Layout */
    .attribute-group {
        background: #111118;
        border: 1px solid #2a2a3a;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
    }
    
    .attribute-group h4 {
        color: white;
        font-size: 18px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #2a2a3a;
    }
    
    /* Responsive Grid for Attribute Cards */
    .attribute-options-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    
    /* Attribute Card Style */
    .attribute-card {
        background: #1a1a2e;
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 16px 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .attribute-card:hover {
        border-color: #3b82f6;
        transform: translateY(-4px);
        background: #1e293b;
    }
    
    .attribute-card.selected {
        border-color: #3b82f6;
        background: #1e293b;
        box-shadow: 0 0 20px rgba(59,130,246,0.2);
    }
    
    .attribute-card img {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }
    
    .attribute-card .attribute-label {
        color: white;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        line-height: 1.3;
    }
    
    .required-badge {
        background: #ef4444;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 8px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 992px) {
        .evaluate-grid {
            grid-template-columns: 1fr;
        }
        
        .summary-sidebar {
            display: none;
        }
        
        .attribute-options-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .attribute-card {
            padding: 12px 8px;
        }
        
        .attribute-card img {
            width: 40px;
            height: 40px;
        }
        
        .attribute-card .attribute-label {
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .attribute-options-grid {
            gap: 10px;
        }
        
        .attribute-card {
            padding: 10px 6px;
        }
        
        .attribute-card img {
            width: 35px;
            height: 35px;
        }
    }
    
    @media (max-width: 768px) {
        .container {
            padding: 0 15px !important;
        }
        
        .question-card {
            padding: 18px !important;
        }
        
        .question-title {
            font-size: 16px !important;
        }
        
        .question-desc {
            font-size: 13px !important;
            margin-left: 0 !important;
        }
        
        .answer-buttons {
            margin-left: 0 !important;
            flex-direction: column;
        }
        
        .answer-btn {
            justify-content: center;
        }
        
        .number-badge {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
        
        .next-btn, .prev-btn, .final-btn {
            padding: 12px 20px;
            font-size: 14px;
            flex: 1;
        }
    }
    
    .summary-sidebar {
        background: #111118;
        border: 1px solid #2a2a3a;
        border-radius: 20px;
        padding: 25px;
        position: sticky;
        top: 120px;
        height: fit-content;
    }
    
    .summary-header h3 {
        color: white;
        font-size: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #2a2a3a;
        padding-bottom: 15px;
    }
    
    .summary-content h4 {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .summary-content h5 {
        color: #3b82f6;
        font-size: 13px;
        margin-bottom: 10px;
    }
    
    .summary-list, .attributes-list {
        margin-bottom: 20px;
    }
    
    .summary-item {
        padding: 8px 0;
        border-bottom: 1px solid #1e1e2a;
        font-size: 13px;
        color: #94a3b8;
    }
    
    .empty-message {
        color: #64748b;
        font-size: 13px;
    }
    
    .mobile-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 100000;
        justify-content: center;
        align-items: center;
    }
    
    .mobile-modal-content {
        background: #111118;
        width: 90%;
        max-width: 400px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #2a2a3a;
    }
    
    .mobile-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #2a2a3a;
    }
    
    .mobile-modal-header h3 {
        color: white;
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 28px;
        cursor: pointer;
    }
    
    .mobile-modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {

    // DOM Elements
    const $questions = $('.question-card');
    const $questionsContainer = $('#questionsContainer');
    const $attributesSection = $('#attributesSection');
    const $attributesContainer = $('#attributesContainer');
    const $prevBtn = $('#prevBtn');
    const $nextGroupBtn = $('#nextGroupBtn');
    const $finalBtn = $('#finalBtn');
    const $summaryList = $('#summaryList');
    const $attributesSummary = $('#attributesSummary');
    const $attributesList = $('#attributesList');
    const $viewSummaryBtn = $('#viewSummaryBtn');
    const $mobileModal = $('#mobileSummaryModal');
    const $closeModal = $('.close-modal');
    const $progressSteps = $('#progressSteps');
    const $currentStepNum = $('#currentStepNum');
    const $totalStepsNum = $('#totalStepsNum');
    const $stepName = $('#stepName');
    const $groupHeading = $('#groupHeading');
    const $groupDescription = $('#groupDescription');
    
    // Data variables
    let answers = {};
    let allAttributeData = [];
    let groupedAttributes = {};
    let groupOrder = [];
    let currentGroupIndex = 0;
    let selectedRadioValues = {};
    let selectedCheckboxValues = {};
    let isSubmitting = false;
    
    const modelId = {{ $model->id }};
    const modelSlug = '{{ $model->sef_url }}';
    const variantSlug = '{{ $variant_slug }}';
    initializeButtonHandlers();

    // Get all question IDs in order
    let questionOrder = [];
    $questions.each(function() {
        questionOrder.push($(this).data('id'));
    });
    const totalQuestions = questionOrder.length;
    
    function areAllQuestionsAnswered() {
        for (let i = 0; i < questionOrder.length; i++) {
            const qId = questionOrder[i];
            if (!answers[qId]) {
                return false;
            }
        }
        return true;
    }

    function initializeButtonHandlers() {
    $prevBtn.off('click').on('click', goToPrevGroup);
    // Default next button handler (will be overridden when needed)
    $nextGroupBtn.off('click').on('click', goToNextGroup);
    $finalBtn.off('click').on('click', handleFinalSubmit);
}

    
    function hasNoAnswer() {
        for (let i = 0; i < questionOrder.length; i++) {
            const qId = questionOrder[i];
            if (answers[qId] === 'no') {
                return true;
            }
        }
        return false;
    }
    
    function getFirstNoIndex() {
        for (let i = 0; i < questionOrder.length; i++) {
            const qId = questionOrder[i];
            if (answers[qId] === 'no') {
                return i;
            }
        }
        return -1;
    }
    
    // Get only YES answered question IDs for API
    function getYesQuestionIds() {
        const yesIds = [];
        for (let i = 0; i < questionOrder.length; i++) {
            const qId = questionOrder[i];
            if (answers[qId] === 'yes') {
                yesIds.push(qId);
            }
        }
        return yesIds;
    }
    
  function updateQuestionVisibility() {
    const firstNoIndex = getFirstNoIndex();
    
    $questions.each(function(index) {
        if (firstNoIndex !== -1 && index > firstNoIndex) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
    
    if (areAllQuestionsAnswered() && !hasNoAnswer()) {
        // ALL QUESTIONS ANSWERED WITH YES
        $questionsContainer.hide();
        $attributesSection.show();
        $progressSteps.show();
        loadAttributes();
    } else if (firstNoIndex !== -1) {
        // HAS NO ANSWER
        if (firstNoIndex === 0) {
            // First question is NO - Show Finish button (exit)
            $questionsContainer.show();
            $attributesSection.hide();
            $progressSteps.hide();
            $prevBtn.hide();
            $nextGroupBtn.hide();
            $finalBtn.show().css('justifyContent', 'center');
            $finalBtn.off('click').on('click', handleEarlyFinish);
        } else {
            // Question 2, 3, or 4 is NO - Show Next button to load attributes
            $questionsContainer.show();
            $attributesSection.hide();
            $progressSteps.hide();
            $prevBtn.hide();
            $nextGroupBtn.show().css('justifyContent', 'center');
            $nextGroupBtn.text('Next →');
            $finalBtn.hide();
            // This Next button will load attributes (not exit)
            $nextGroupBtn.off('click').on('click', handleNoAnswerNext);
        }
    } else {
        // Not all questions answered yet, no NO answers
        $questionsContainer.show();
        $attributesSection.hide();
        $progressSteps.hide();
        $prevBtn.hide();
        $nextGroupBtn.hide();
        $finalBtn.hide();
    }
}
    
function handleNoAnswerNext() {
    // This is called when user answered NO to Q2, Q3, or Q4
    // But previous questions were YES, so we still need to load attributes
    
    console.log('No answer detected (not Q1), loading attributes with YES answers only');
    
    // Hide questions container and show attributes section
    $questionsContainer.hide();
    $attributesSection.show();
    $progressSteps.show();
    
    // Reset button states for attribute navigation
    $prevBtn.hide();
    $nextGroupBtn.show().css('justifyContent', 'center');
    $nextGroupBtn.text('Next');
    $finalBtn.hide();
    
    // Ensure next button handler is set for attribute navigation
    $nextGroupBtn.off('click').on('click', goToNextGroup);
    
    // Load attributes (will only include YES answers)
    loadAttributes();
}

    function canAnswerQuestion(questionId) {
        const questionIndex = questionOrder.indexOf(questionId);
        if (questionIndex === -1) return false;
        
        for (let i = 0; i < questionIndex; i++) {
            const prevQuestionId = questionOrder[i];
            if (!answers[prevQuestionId]) {
                return false;
            }
        }
        return true;
    }
    
   function loadAttributes() {
    const yesQuestionIds = getYesQuestionIds();
    
    if (yesQuestionIds.length === 0) return;
    
    const selectedId = yesQuestionIds.join(',');
    
    // Show loading state
    $attributesContainer.html('<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 30px; color: #3b82f6;"></i><p style="color: #94a3b8; margin-top: 10px;">Loading options...</p></div>');
    
    $.ajax({
        url: `/get_attributes?selected_id=${selectedId}&model_id=${modelId}`,
        method: 'GET',
        dataType: 'json',
        success: function(result) {
            if (result.status === 'success' && result.data.length > 0) {
                groupAttributesByGroup(result.data);
                // Ensure next button has correct handler for attribute navigation
                $nextGroupBtn.off('click').on('click', goToNextGroup);
            } else {
                showFinalStep();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading attributes:', error);
            $attributesContainer.html('<div style="text-align: center; padding: 40px; color: #ef4444;">Error loading options. Please refresh the page.</div>');
        }
    });
}
    
    function groupAttributesByGroup(attributes) {
        groupedAttributes = {};
        
        attributes.forEach(attr => {
            const groupId = attr.group_attributes;
            if (!groupedAttributes[groupId]) {
                groupedAttributes[groupId] = [];
            }
            groupedAttributes[groupId].push(attr);
        });
        
        groupOrder = Object.keys(groupedAttributes).map(Number).sort((a, b) => a - b);
        $totalStepsNum.text(groupOrder.length);
        currentGroupIndex = 0;
        
        if (groupOrder.length > 0) {
            displayGroup(groupOrder[currentGroupIndex]);
            updateNavigationButtons();
            updateStepIndicator();
        } else {
            showFinalStep();
        }
    }
    
    function displayGroup(groupId) {
        const attributes = groupedAttributes[groupId];
        if (!attributes || attributes.length === 0) return;
        
        const groupedByFieldId = {};
        attributes.forEach(attr => {
            const fieldId = attr.product_field_id;
            if (!groupedByFieldId[fieldId]) {
                groupedByFieldId[fieldId] = [];
            }
            groupedByFieldId[fieldId].push(attr);
        });
        
        let html = '';
        
        for (const [fieldId, options] of Object.entries(groupedByFieldId)) {
            const isRequired = options[0].is_required === 1;
            const inputType = options[0].input_type;
            const title = options[0].title;
            
            html += `<div class="attribute-group" data-field-id="${fieldId}" data-input-type="${inputType}" data-required="${isRequired}">`;
            html += `<h4>${escapeHtml(title)} ${isRequired ? '<span class="required-badge">Required</span>' : '<span class="required-badge" style="background: #64748b;">Optional</span>'}</h4>`;
            
            // Sort options by sort_order
            options.sort((a, b) => a.sort_order - b.sort_order);
            
            html += `<div class="attribute-options-grid">`;
            
            options.forEach(option => {
                let isSelected = false;
                if (inputType === 'checkbox') {
                    isSelected = selectedCheckboxValues[fieldId] && selectedCheckboxValues[fieldId].includes(option.id);
                } else {
                    isSelected = selectedRadioValues[fieldId] === option.id;
                }
                
                html += `
                    <div class="attribute-card ${isSelected ? 'selected' : ''}" 
                         data-field-id="${fieldId}" 
                         data-option-id="${option.id}" 
                         data-label="${escapeHtml(option.label)}" 
                         data-input-type="${inputType}">
                        ${option.icon_url ? `<img src="${option.icon_url}" alt="${escapeHtml(option.label)}" loading="lazy">` : '<i class="fas fa-mobile-alt" style="font-size: 40px; color: #3b82f6;"></i>'}
                        <span class="attribute-label">${escapeHtml(option.label)}</span>
                    </div>
                `;
            });
            
            html += `</div></div>`;
        }
        
        $attributesContainer.html(html);
        
        const firstAttr = attributes[0];
        $groupHeading.text(firstAttr.title || `Step ${groupId}`);
        $groupDescription.text(`Please select the condition of your device`);
        
        attachAttributeHandlers();
        updateAttributesSummary();
        updateMobileSummary();
    }
    
    function attachAttributeHandlers() {
        $('.attribute-card').off('click').on('click', function() {
            const $card = $(this);
            const fieldId = $card.data('field-id');
            const optionId = $card.data('option-id');
            const inputType = $card.data('input-type');
            const $group = $card.closest('.attribute-group');
            const groupInputType = $group.data('input-type');
            
            if (groupInputType === 'checkbox' || inputType === 'checkbox') {
                // Checkbox: toggle selection
                $card.toggleClass('selected');
                
                if (!selectedCheckboxValues[fieldId]) {
                    selectedCheckboxValues[fieldId] = [];
                }
                
                if ($card.hasClass('selected')) {
                    if (!selectedCheckboxValues[fieldId].includes(optionId)) {
                        selectedCheckboxValues[fieldId].push(optionId);
                    }
                } else {
                    const index = selectedCheckboxValues[fieldId].indexOf(optionId);
                    if (index !== -1) {
                        selectedCheckboxValues[fieldId].splice(index, 1);
                    }
                }
            } else {
                // Radio: single selection
                $group.find('.attribute-card').removeClass('selected');
                $card.addClass('selected');
                selectedRadioValues[fieldId] = optionId;
            }
            
            updateAttributesSummary();
            updateMobileSummary();
        });
    }
    
    function isCurrentGroupValid() {
        const currentGroupId = groupOrder[currentGroupIndex];
        const attributes = groupedAttributes[currentGroupId];
        
        if (!attributes) return true;
        
        const groupedByFieldId = {};
        attributes.forEach(attr => {
            const fieldId = attr.product_field_id;
            if (!groupedByFieldId[fieldId]) {
                groupedByFieldId[fieldId] = [];
            }
            groupedByFieldId[fieldId].push(attr);
        });
        
        let isValid = true;
        
        for (const [fieldId, options] of Object.entries(groupedByFieldId)) {
            const isRequired = options[0].is_required === 1;
            const inputType = options[0].input_type;
            
            if (isRequired) {
                if (inputType === 'checkbox') {
                    if (!selectedCheckboxValues[fieldId] || selectedCheckboxValues[fieldId].length === 0) {
                        isValid = false;
                        break;
                    }
                } else {
                    if (!selectedRadioValues[fieldId]) {
                        isValid = false;
                        break;
                    }
                }
            }
        }
        
        return isValid;
    }
    
    function showRequiredError() {
        alert('Please select all required options before proceeding.');
    }
    
   function goToNextGroup() {
    console.log('goToNextGroup called - currentGroupIndex:', currentGroupIndex, 'groupOrder:', groupOrder);
    
    if (!isCurrentGroupValid()) {
        showRequiredError();
        return;
    }
    
    if (currentGroupIndex < groupOrder.length - 1) {
        currentGroupIndex++;
        displayGroup(groupOrder[currentGroupIndex]);
        updateNavigationButtons();
        updateStepIndicator();
        $('html, body').animate({ scrollTop: $attributesSection.offset().top - 100 }, 300);
    } else {
        console.log('At last group, should show final button');
        updateNavigationButtons();
    }
}
    
    function goToPrevGroup() {
        if (currentGroupIndex > 0) {
            currentGroupIndex--;
            displayGroup(groupOrder[currentGroupIndex]);
            updateNavigationButtons();
            updateStepIndicator();
            $('html, body').animate({ scrollTop: $attributesSection.offset().top - 100 }, 300);
        }
    }
    
    function updateNavigationButtons() {
        if (currentGroupIndex === 0) {
            $prevBtn.hide();
        } else {
            $prevBtn.show();
        }
        
        if (currentGroupIndex === groupOrder.length - 1) {
            $nextGroupBtn.hide();
            $finalBtn.show();
        } else {
            $nextGroupBtn.show();
            $finalBtn.hide();
        }
    }
    
    function updateStepIndicator() {
        $currentStepNum.text(currentGroupIndex + 1);
        const currentGroupId = groupOrder[currentGroupIndex];
        const attributes = groupedAttributes[currentGroupId];
        if (attributes && attributes.length > 0) {
            $stepName.text(attributes[0].title || `Step ${currentGroupId}`);
        }
    }
    
    function showFinalStep() {
        $progressSteps.hide();
        $nextGroupBtn.hide();
        $prevBtn.hide();
        $finalBtn.show().css('justifyContent', 'center');
        $attributesContainer.html('<div style="text-align: center; padding: 40px; color: #94a3b8;">No additional options available. Click Finish to complete evaluation.</div>');
    }
    
    function updateAttributesSummary() {
        let hasSelections = Object.keys(selectedRadioValues).length > 0 || Object.keys(selectedCheckboxValues).length > 0;
        
        if (!hasSelections) {
            $attributesSummary.hide();
            return;
        }
        
        $attributesSummary.show();
        let html = '';
        
        for (const groupId of groupOrder) {
            const attributes = groupedAttributes[groupId];
            if (!attributes) continue;
            
            const groupedByFieldId = {};
            attributes.forEach(attr => {
                const fieldId = attr.product_field_id;
                if (!groupedByFieldId[fieldId]) {
                    groupedByFieldId[fieldId] = [];
                }
                groupedByFieldId[fieldId].push(attr);
            });
            
            for (const [fieldId, options] of Object.entries(groupedByFieldId)) {
                const title = options[0].title;
                let selectedLabels = [];
                
                // Check radio selection
                if (selectedRadioValues[fieldId]) {
                    const selectedOption = options.find(opt => opt.id == selectedRadioValues[fieldId]);
                    if (selectedOption) {
                        selectedLabels.push(selectedOption.label);
                    }
                }
                
                // Check checkbox selection
                if (selectedCheckboxValues[fieldId]) {
                    selectedCheckboxValues[fieldId].forEach(optId => {
                        const selectedOption = options.find(opt => opt.id == optId);
                        if (selectedOption) {
                            selectedLabels.push(selectedOption.label);
                        }
                    });
                }
                
                if (selectedLabels.length > 0) {
                    html += `<div style="margin-bottom: 15px;">`;
                    html += `<strong style="color: white;">${escapeHtml(title)}:</strong>`;
                    html += `<div style="margin-top: 5px;">`;
                    selectedLabels.forEach(label => {
                        html += `<div style="color: #3b82f6; margin-top: 3px;">✓ ${escapeHtml(label)}</div>`;
                    });
                    html += `</div></div>`;
                }
            }
        }
        
        $attributesList.html(html || '<p class="empty-message">No options selected yet</p>');
    }
    
    function updateSummary() {
        const answeredQuestions = Object.keys(answers);
        
        if (answeredQuestions.length === 0) {
            $summaryList.html('<p class="empty-message">No questions answered yet</p>');
            return;
        }
        
        let summaryHtml = '';
        for (const [id, answer] of Object.entries(answers)) {
            const $question = $(`.answer-btn[data-id="${id}"]`);
            const questionName = $question.length ? $question.data('name') : 'Question';
            const answerText = answer === 'yes' ? 'Yes' : 'No';
            const icon = answer === 'yes' ? '✅' : '❌';
            summaryHtml += `<div class="summary-item">${icon} ${answerText}, ${questionName}</div>`;
        }
        $summaryList.html(summaryHtml);
        updateMobileSummary();
    }
    
    function updateMobileSummary() {
        const $mobileSummaryContent = $('#mobileSummaryContent');
        if (!$mobileSummaryContent.length) return;
        
        const answeredQuestions = Object.keys(answers);
        let summaryHtml = '<h5 style="color: #3b82f6; margin-bottom: 10px;">Screening Question:</h5>';
        
        if (answeredQuestions.length === 0) {
            summaryHtml += '<p class="empty-message">No questions answered yet</p>';
        } else {
            for (const [id, answer] of Object.entries(answers)) {
                const $question = $(`.answer-btn[data-id="${id}"]`);
                const questionName = $question.length ? $question.data('name') : 'Question';
                const answerText = answer === 'yes' ? 'Yes' : 'No';
                const icon = answer === 'yes' ? '✅' : '❌';
                summaryHtml += `<div class="summary-item">${icon} ${answerText}, ${questionName}</div>`;
            }
        }
        
        if ($attributesSummary.is(':visible')) {
            summaryHtml += '<h5 style="color: #3b82f6; margin-top: 20px;">Device Condition:</h5>';
            summaryHtml += $('#attributesList').html() || '<p class="empty-message">No options selected yet</p>';
        }
        
        $mobileSummaryContent.html(summaryHtml);
    }
    
    function handleAnswer($button, questionId, questionName, value) {
        if (!canAnswerQuestion(questionId)) {
            alert('Please answer the previous questions first in order.');
            return;
        }
        
        answers[questionId] = value;
        
        const $parentCard = $button.closest('.question-card');
        const $yesBtn = $parentCard.find('.yes-btn');
        const $noBtn = $parentCard.find('.no-btn');
        
        if (value === 'yes') {
            $yesBtn.addClass('active');
            $noBtn.removeClass('active');
        } else {
            $noBtn.addClass('active');
            $yesBtn.removeClass('active');
        }
        
        updateSummary();
        updateQuestionVisibility();
    }
    
    function handleFinalSubmit() {
        let allRequiredFilled = true;
        
        for (const groupId of groupOrder) {
            const attributes = groupedAttributes[groupId];
            if (!attributes) continue;
            
            const groupedByFieldId = {};
            attributes.forEach(attr => {
                const fieldId = attr.product_field_id;
                if (!groupedByFieldId[fieldId]) {
                    groupedByFieldId[fieldId] = [];
                }
                groupedByFieldId[fieldId].push(attr);
            });
            
            for (const [fieldId, options] of Object.entries(groupedByFieldId)) {
                const isRequired = options[0].is_required === 1;
                const inputType = options[0].input_type;
                
                if (isRequired) {
                    if (inputType === 'checkbox') {
                        if (!selectedCheckboxValues[fieldId] || selectedCheckboxValues[fieldId].length === 0) {
                            allRequiredFilled = false;
                            break;
                        }
                    } else {
                        if (!selectedRadioValues[fieldId]) {
                            allRequiredFilled = false;
                            break;
                        }
                    }
                }
            }
            if (!allRequiredFilled) break;
        }
        
        if (!allRequiredFilled && groupOrder.length > 0) {
            alert('Please complete all required selections before finishing.');
            return;
        }
        
        if (isSubmitting) return;
        isSubmitting = true;
        
        $finalBtn.prop('disabled', true);
        $finalBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        
        const finalData = {
            model_id: modelId,
            model_slug: modelSlug,
            variant_slug: variantSlug,
            screening_answers: answers,
            selected_radio_attributes: selectedRadioValues,
            selected_checkbox_attributes: selectedCheckboxValues,
            has_issue: hasNoAnswer(),
            issue_question: hasNoAnswer() ? getFirstNoIndex() + 1 : null
        };
        
        console.log('Final Submission Data:', finalData);
        sessionStorage.setItem('evaluationData', JSON.stringify(finalData));
        
        setTimeout(function() {
            alert('Evaluation completed successfully!');
            isSubmitting = false;
            $finalBtn.prop('disabled', false);
            $finalBtn.html('Finish Evaluation <i class="fas fa-check-circle"></i>');
        }, 1000);
    }
    
    function handleEarlyFinish() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        const finalData = {
            model_id: modelId,
            model_slug: modelSlug,
            variant_slug: variantSlug,
            screening_answers: answers,
            has_issue: true,
            issue_question: getFirstNoIndex() + 1
        };
        
        sessionStorage.setItem('evaluationData', JSON.stringify(finalData));
        alert('Thank you for completing the evaluation. Our executive will contact you soon.');
        isSubmitting = false;
    }

  function handleFinalSubmit() {
    let allRequiredFilled = true;
    
    for (const groupId of groupOrder) {
        const attributes = groupedAttributes[groupId];
        if (!attributes) continue;
        
        const groupedByFieldId = {};
        attributes.forEach(attr => {
            const fieldId = attr.product_field_id;
            if (!groupedByFieldId[fieldId]) {
                groupedByFieldId[fieldId] = [];
            }
            groupedByFieldId[fieldId].push(attr);
        });
        
        for (const [fieldId, options] of Object.entries(groupedByFieldId)) {
            const isRequired = options[0].is_required === 1;
            const inputType = options[0].input_type;
            
            if (isRequired) {
                if (inputType === 'checkbox') {
                    if (!selectedCheckboxValues[fieldId] || selectedCheckboxValues[fieldId].length === 0) {
                        allRequiredFilled = false;
                        break;
                    }
                } else {
                    if (!selectedRadioValues[fieldId]) {
                        allRequiredFilled = false;
                        break;
                    }
                }
            }
        }
        if (!allRequiredFilled) break;
    }
    
    if (!allRequiredFilled && groupOrder.length > 0) {
        alert('Please complete all required selections before finishing.');
        return;
    }
    
    if (isSubmitting) return;
    isSubmitting = true;
    
    $finalBtn.prop('disabled', true);
    $finalBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
    
    // Build the complete submission data
    const finalData = {
        model_id: modelId,
        model_slug: modelSlug,
        variant_slug: variantSlug,
        screening_answers: answers,
        selected_radio_attributes: selectedRadioValues,
        selected_checkbox_attributes: selectedCheckboxValues,
        has_issue: hasNoAnswer(),
        issue_question: hasNoAnswer() ? getFirstNoIndex() + 1 : null
    };
    
    console.log('Final Submission Data:', finalData);
    
    // Prepare payload for price calculation API
    const payload = {
        model_id: modelId,
        variant_slug: variantSlug,
        answers: answers,
        selected_attributes: {
            ...selectedRadioValues,
            ...selectedCheckboxValues
        }
    };
    
    console.log('Sending to get_price:', payload);
    
    // 1. First, call the price calculation API
    $.ajax({
        url: '/api/get_price',  // Update this to your actual endpoint
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function(response) {
            console.log('Price API Response:', response);
            
            if (response.status === 'success') {
                // Add price to final data
                finalData.calculated_price = response.price;
                finalData.base_price = response.base_price;
                finalData.deductions = response.deductions;
                
                // 2. Then call cart API to save the evaluation
                $.ajax({
                    url: '/api/get_price',  // Update this to your actual endpoint
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(finalData),
                    success: function(cartResponse) {
                        console.log('Cart API Response:', cartResponse);
                        
                        if (cartResponse.status === 'success') {
                            // Save to sessionStorage as backup
                            sessionStorage.setItem('evaluationData', JSON.stringify(finalData));
                            
                            alert('Evaluation completed successfully!');
                            
                            // Optional: Redirect to next page (cart or offer page)
                            if (cartResponse.redirect_url) {
                                window.location.href = cartResponse.redirect_url;
                            }
                        } else {
                            alert('Error saving evaluation: ' + (cartResponse.message || 'Please try again'));
                            $finalBtn.prop('disabled', false);
                            $finalBtn.html('Finish Evaluation <i class="fas fa-check-circle"></i>');
                        }
                        isSubmitting = false;
                    },
                    error: function(xhr, status, error) {
                        console.error('Cart API Error:', error, xhr.responseText);
                        alert('Failed to save evaluation. Please try again.');
                        $finalBtn.prop('disabled', false);
                        $finalBtn.html('Finish Evaluation <i class="fas fa-check-circle"></i>');
                        isSubmitting = false;
                    }
                });
            } else {
                alert('Price calculation failed: ' + (response.message || 'Please try again'));
                $finalBtn.prop('disabled', false);
                $finalBtn.html('Finish Evaluation <i class="fas fa-check-circle"></i>');
                isSubmitting = false;
            }
        },
        error: function(xhr, status, error) {
            console.error('Price API Error:', error, xhr.responseText);
            alert('Failed to calculate price. Please try again.');
            $finalBtn.prop('disabled', false);
            $finalBtn.html('Finish Evaluation <i class="fas fa-check-circle"></i>');
            isSubmitting = false;
        }
    });
}
    
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    // Event Listeners
    $('.answer-btn').on('click', function() {
        const $this = $(this);
        const questionId = $this.data('id');
        const questionName = $this.data('name');
        const value = $this.data('value');
        handleAnswer($this, questionId, questionName, value);
    });
    
    $prevBtn.on('click', goToPrevGroup);
    $nextGroupBtn.on('click', goToNextGroup);
    $finalBtn.on('click', handleFinalSubmit);
    
    $viewSummaryBtn.on('click', function() {
        if ($(window).width() <= 992) {
            $mobileModal.show();
            updateMobileSummary();
        }
    });
    
    $closeModal.on('click', function() {
        $mobileModal.hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).is('#mobileSummaryModal')) {
            $mobileModal.hide();
        }
    });
    
    updateQuestionVisibility();
});
</script>
@endsection