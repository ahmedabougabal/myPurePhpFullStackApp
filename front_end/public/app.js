let categories = [];
let courses = [];
let selectedCategoryId = null;

async function fetchData() {
    try {
        const [categoriesResponse, coursesResponse] = await Promise.all([
            fetch('http://api.cc.localhost:8090/categories'),
            fetch('http://api.cc.localhost:8090/courses')
        ]);

        categories = await categoriesResponse.json();
        courses = await coursesResponse.json();

        renderCategories();
        renderCourses();
    } catch (error) {
        console.error('Error fetching data:', error);
        document.getElementById('categories-list').innerHTML = '<p>Error loading data</p>';
    }
}

function getCategoryDepth(categoryId, depth = 1) {
    const category = categories.find(cat => cat.id === categoryId);
    if (!category || !category.parent_id || depth >= 4) return depth;
    return getCategoryDepth(category.parent_id, depth + 1);
}

function getCoursesCount(categoryId) {
    const directCourses = courses.filter(course => course.category_id === categoryId).length;
    const childCategories = categories.filter(cat => cat.parent_id === categoryId);
    const childCourses = childCategories.reduce((sum, child) => sum + getCoursesCount(child.id), 0);
    return directCourses + childCourses;
}

function getRootCategory(categoryId) {
    const category = categories.find(cat => cat.id === categoryId);
    if (!category || !category.parent_id) return category;
    return getRootCategory(category.parent_id);
}

function renderCategories() {
    const container = document.getElementById('categories-list');
    container.innerHTML = '';

    function createCategoryElement(category, depth = 0) {
        const count = getCoursesCount(category.id);
        const element = document.createElement('div');
        element.className = `category-item ${selectedCategoryId === category.id ? 'active' : ''} ${depth > 0 ? 'subcategory' : ''}`;
        element.innerHTML = `
            <span class="category-name">${category.name}</span>
            <span class="category-count">(${count})</span>
        `;
        element.onclick = (e) => {
            e.stopPropagation();
            selectedCategoryId = category.id === selectedCategoryId ? null : category.id;
            renderCategories();
            renderCourses();
            updatePageTitle();
        };
        return element;
    }

    function renderCategoryTree(parentId = null, depth = 0) {
        const categoryItems = categories
            .filter(cat => cat.parent_id === parentId)
            .sort((a, b) => a.name.localeCompare(b.name));

        categoryItems.forEach(category => {
            const element = createCategoryElement(category, depth);
            container.appendChild(element);
            renderCategoryTree(category.id, depth + 1);
        });
    }

    renderCategoryTree();
}

function renderCourses() {
    const container = document.getElementById('courses-container');
    container.innerHTML = '';

    let filteredCourses = courses;
    if (selectedCategoryId) {
        const childCategories = getAllChildCategories(selectedCategoryId);
        const categoryIds = [selectedCategoryId, ...childCategories.map(c => c.id)];
        filteredCourses = courses.filter(course => categoryIds.includes(course.category_id));
    }

    filteredCourses.forEach(course => {
        const rootCategory = getRootCategory(course.category_id);
        const element = document.createElement('div');
        element.className = 'course-card';
        element.innerHTML = `
            <div class="course-image">
                ${course.image_preview ? `
                    <img src="${course.image_preview}" alt="${course.name}" loading="lazy">
                    <div class="category-badge">${rootCategory?.name || 'Uncategorized'}</div>
                ` : ''}
            </div>
            <div class="course-content">
                <h3 class="course-title">${course.name}</h3>
                <p class="course-description">${course.description}</p>
            </div>
        `;
        container.appendChild(element);
    });
}

function updatePageTitle() {
    const header = document.querySelector('header h1');
    if (selectedCategoryId) {
        const category = categories.find(cat => cat.id === selectedCategoryId);
        header.textContent = category.name;
    } else {
        header.textContent = 'Course catalog';
    }
}

// helper function
function getAllChildCategories(categoryId) {
    const children = categories.filter(cat => cat.parent_id === categoryId);
    return children.reduce((acc, child) => {
        return [...acc, child, ...getAllChildCategories(child.id)];
    }, []);
}

fetchData();