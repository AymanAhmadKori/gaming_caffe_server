// Create database element \\
const DB = new Dexie(
  "DB-admin-" +
  getPayload(localStorage.getItem('JWT'))['id']
);

DB.version(.1).stores({
  groups     : "++id, name, entry_date",
  categories : "++id, group_id, name, entry_date",
  booking    : "++id, category_id",
  bookingLog : "++id, category_id, entry_date"
});

// === Groups === \\
async function DB_getAllGroups() {
  return await DB.groups.toArray()
}
async function DB_getAllGroupsNames() {
  let groups = await DB_getAllGroups();
  return groups.map(group => group.name);
}

// Add group to database \\
async function DB_addGroup(name, icon_name) {
  // Group data \\
  let data = {
    icon_name,
    name,
    entry_date : getCurrentUTC()
  };
  
  // Add group in database & get id \\
  data.id = await DB.groups.add(data);

  // Return group data with id \\
  return data;
}

async function DB_getGroup(id) {
  return await DB.groups.get(id)
}
async function DB_deleteGroup(id) {
  return await DB.groups.delete(id)
}
async function DB_updateGroup(id, data) {
  return await DB.groups.update(id, data)
}

//=== Categories ===\\
async function DB_addCatigory(name, icon_name, group_id = null) {
  // Defualt category data \\
  let data = {
    icon_name: icon_name,
    name: name,
    status : true,
    group_id,
    price: 10,
    time: 60,
    entry_date : getCurrentUTC()
  };
  
  // Add category in database & get id \\
  data.id = await DB.categories.add(data);

  // Return category data with id \\
  return data;
}

// Get all categories \\
async function DB_getAllCategories() {
  return await DB.categories.toArray();
}

// Bulk get categories \\
async function DB_bulkGetCategories(ids) {
  return await DB.categories.bulkGet(ids);
}

// Delete category \\
async function DB_deleteCategory(id) {
  return await DB.categories.delete(id)
}

// Update category \\
async function DB_updateCategory(id, data) {
  return await DB.categories.update(id, data);
}

//=== Booking ===\\
// Create booking \\
async function DB_createNewBooking(category_id, timer_data, time, price) {
  // Get booking data \\
  let data = {
    category_id,
    timer_data,
    time,
    price
  }

  // Save in data base & get booking id \\
  data.id = await DB.booking.add(data);
  
  // Return booking data \\
  return data;
}

// Update booking \\
async function DB_updateBooking(booking_id, data) {
  return await DB.booking.update(booking_id, data);
}

async function DB_bulkUpdateBookings(data) {
  return await DB.transaction('rw', DB.booking, async () => {
    data.map(async booking => {
      // Get booking id \\
      const id = booking.id;
      
      // Remove booking id from booking object \\
      delete booking.id;
      
      // Update booking \\
      await DB.booking.update(id, booking);
    })
  });
}

// Get all booking \\
async function DB_getAllBooking() {
  return await DB.booking.toArray()
}

// Delete booking \\
async function DB_deleteBooking(id) {
  return await DB.booking.delete(id);
}

// === Booking Log === \\
async function DB_addBookingLog(name, icon_name, time, price, totalElapsedTime, total_price) {
  let data = {
    name,
    icon_name,
    time,
    price,
    totalElapsedTime,
    total_price,
    entry_date: getCurrentUTC()
  }
  
  return await DB.bookingLog.add(data);
}

async function DB_getAllBookingLogs() {
  return await DB.bookingLog.toArray();
}